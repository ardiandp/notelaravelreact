import { useState, useEffect, useCallback } from 'react'
import api from '../api/axios'
import CameraModal from '../components/CameraModal'

export default function AttendancePage() {
  const [state, setState] = useState(null)
  const [loading, setLoading] = useState(true)
  const [history, setHistory] = useState([])
  const [bulan, setBulan] = useState(new Date().getMonth() + 1)
  const [tahun, setTahun] = useState(new Date().getFullYear())
  const [correctionModal, setCorrectionModal] = useState(null)
  const [alasan, setAlasan] = useState('')
  const [cameraFor, setCameraFor] = useState(null)

  const fetchToday = useCallback(async () => {
    try {
      const res = await api.get('/attendance/today')
      setState(res.data)
    } catch { setState({ checked_in: false, checked_out: false, has_schedule: false }) }
  }, [])

  const fetchHistory = useCallback(async () => {
    try {
      const res = await api.get('/attendance/history', { params: { bulan, tahun } })
      setHistory(res.data)
    } catch {}
  }, [bulan, tahun])

  useEffect(() => { fetchToday().finally(() => setLoading(false)) }, [fetchToday])
  useEffect(() => { fetchHistory() }, [fetchHistory])

  const confirmAttendance = async (type, foto, pos) => {
    try {
      const payload = {
        lat: pos.coords.latitude,
        lon: pos.coords.longitude,
        address: 'Lokasi anda',
        foto,
      }
      if (type === 'in') payload.work_from = state?.work_from || 'wfo'

      if (type === 'in') {
        await api.post('/attendance/check-in', payload)
      } else {
        await api.post('/attendance/check-out', payload)
      }
      setCameraFor(null)
      fetchToday()
    } catch (err) {
      alert(err.response?.data?.message || 'Error')
    }
  }

  const handleAttendance = (type) => {
    if (!navigator.geolocation) return alert('GPS tidak tersedia')
    setCameraFor(type)
  }

  const handleCameraConfirm = (foto) => {
    navigator.geolocation.getCurrentPosition(
      (pos) => confirmAttendance(cameraFor, foto, pos),
      () => alert('Gagal mendapatkan lokasi GPS')
    )
  }

  const handleCameraClose = () => {
    setCameraFor(null)
  }

  const submitCorrection = async () => {
    try {
      await api.post('/attendance/correction', { attendance_id: correctionModal, alasan })
      setCorrectionModal(null)
      setAlasan('')
      alert('Pengajuan konfirmasi berhasil')
    } catch (err) { alert(err.response?.data?.message || 'Error') }
  }

  const statusBadge = (s) => {
    const m = { hadir: 'bg-green-100 text-green-700', terlambat: 'bg-yellow-100 text-yellow-700', alpha: 'bg-red-100 text-red-700', izin: 'bg-blue-100 text-blue-700', sakit: 'bg-purple-100 text-purple-700', cuti: 'bg-indigo-100 text-indigo-700' }
    return <span className={`text-xs px-2 py-0.5 rounded-full ${m[s] || 'bg-gray-100 text-gray-700'}`}>{s}</span>
  }

  const wfBadge = (wf) => {
    const cls = wf === 'wfa' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-700'
    return <span className={`text-xs px-2 py-0.5 rounded-full font-medium ${cls}`}>{(wf || 'wfo').toUpperCase()}</span>
  }

  const fotoThumb = (path) => {
    if (!path) return null
    return (
      <img
        src={`/storage/${path}`}
        alt="foto"
        className="w-16 h-16 object-cover rounded-lg mx-auto mt-2 border"
      />
    )
  }

  if (loading) return <div className="flex justify-center items-center min-h-screen"><div className="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-500" /></div>

  return (
    <div>
      <div className="bg-blue-50 border border-blue-200 rounded-xl shadow-sm p-6 mb-6 text-center">
        {state?.checked_in ? (
          <>
            {state.attendance && (
              <div className="mb-4">
                <p className="text-sm text-gray-500">Absen Masuk: {state.attendance.check_in?.substring(0, 5)}</p>
                <p className="text-sm text-gray-500">Status: {statusBadge(state.attendance.status)} {wfBadge(state.attendance.work_from || state.work_from)}</p>
                {fotoThumb(state.attendance.check_in_foto)}
                {state.attendance.terlambat_menit > 0 && (
                  <div className="mt-2">
                    <p className="text-sm text-red-500">Terlambat {state.attendance.terlambat_menit} menit</p>
                    <button onClick={() => setCorrectionModal(state.attendance.id)} className="text-xs text-indigo-600 hover:underline">Ajukan konfirmasi keterlambatan</button>
                  </div>
                )}
              </div>
            )}
            {state.checked_out ? (
              <div className="opacity-60 cursor-not-allowed">
                <button disabled className="bg-gray-400 text-white px-8 py-3 rounded-xl text-lg font-medium">Selesai</button>
                <p className="text-green-600 font-medium mt-2">Absen Keluar {state.attendance?.check_out?.substring(0, 5)}</p>
                {fotoThumb(state.attendance.check_out_foto)}
              </div>
            ) : (
              <button onClick={() => handleAttendance('out')} className="bg-red-500 text-white px-8 py-3 rounded-xl text-lg font-medium hover:bg-red-600 transition">Absen Keluar</button>
            )}
          </>
        ) : (
          <>
            <p className="text-sm text-gray-500 mb-3">Jadwal hari ini: {state?.schedule?.nama || '-'} {wfBadge(state?.work_from)}</p>
            {state?.has_schedule === false ? (
              <button disabled className="bg-gray-400 text-white px-8 py-3 rounded-xl text-lg font-medium cursor-not-allowed">Tidak ada jadwal</button>
            ) : (
              <button onClick={() => handleAttendance('in')} className="bg-indigo-600 text-white px-8 py-3 rounded-xl text-lg font-medium hover:bg-indigo-700 transition">Absen Masuk</button>
            )}
          </>
        )}
      </div>

      <div className="bg-white rounded-xl shadow-sm border p-6">
        <div className="flex justify-between items-center mb-4">
          <h2 className="font-semibold">Riwayat</h2>
          <div className="flex gap-2">
            <select value={bulan} onChange={(e) => setBulan(parseInt(e.target.value))} className="border rounded px-2 py-1 text-sm">
              {Array.from({ length: 12 }, (_, i) => <option key={i + 1} value={i + 1}>{new Date(0, i).toLocaleString('id', { month: 'long' })}</option>)}
            </select>
            <select value={tahun} onChange={(e) => setTahun(parseInt(e.target.value))} className="border rounded px-2 py-1 text-sm">
              {[2024, 2025, 2026].map((y) => <option key={y} value={y}>{y}</option>)}
            </select>
          </div>
        </div>
        {history.length === 0 ? <p className="text-gray-400 text-center py-4">Belum ada data</p> : (
          <div className="space-y-2">
            {history.map((h) => (
              <div key={h.id} className="flex items-center justify-between py-2 border-b last:border-0 text-sm">
                <span>{new Date(h.tanggal).toLocaleDateString('id', { day: 'numeric', month: 'short' })}</span>
                {statusBadge(h.status)} {wfBadge(h.work_from)}
                <span className="text-gray-500">{h.check_in?.substring(0, 5) || '-'} - {h.check_out?.substring(0, 5) || '-'}</span>
              </div>
            ))}
          </div>
        )}
      </div>

      {correctionModal && (
        <div className="fixed inset-0 bg-black/40 flex items-center justify-center z-50" onClick={() => setCorrectionModal(null)}>
          <div className="bg-white rounded-xl p-6 max-w-sm w-full mx-4" onClick={(e) => e.stopPropagation()}>
            <h3 className="font-semibold mb-4">Konfirmasi Keterlambatan</h3>
            <textarea value={alasan} onChange={(e) => setAlasan(e.target.value)} className="w-full border rounded-lg p-3 text-sm mb-4" rows={3} placeholder="Alasan keterlambatan..." />
            <div className="flex gap-2">
              <button onClick={submitCorrection} className="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm">Kirim</button>
              <button onClick={() => setCorrectionModal(null)} className="border px-4 py-2 rounded-lg text-sm">Batal</button>
            </div>
          </div>
        </div>
      )}

      {cameraFor && (
        <CameraModal onConfirm={handleCameraConfirm} onClose={handleCameraClose} />
      )}
    </div>
  )
}

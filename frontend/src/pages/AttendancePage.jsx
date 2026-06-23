import { useState, useEffect, useCallback } from 'react'
import api from '../api/axios'
import Navbar from '../components/Navbar'

export default function AttendancePage() {
  const [state, setState] = useState(null)
  const [loading, setLoading] = useState(true)
  const [history, setHistory] = useState([])
  const [bulan, setBulan] = useState(new Date().getMonth() + 1)
  const [tahun, setTahun] = useState(new Date().getFullYear())
  const [correctionModal, setCorrectionModal] = useState(null)
  const [alasan, setAlasan] = useState('')

  const fetchToday = useCallback(async () => {
    try {
      const res = await api.get('/attendance/today')
      setState(res.data)
    } catch { setState({ checked_in: false, checked_out: false }) }
  }, [])

  const fetchHistory = useCallback(async () => {
    try {
      const res = await api.get('/attendance/history', { params: { bulan, tahun } })
      setHistory(res.data)
    } catch {}
  }, [bulan, tahun])

  useEffect(() => { fetchToday().finally(() => setLoading(false)) }, [fetchToday])
  useEffect(() => { fetchHistory() }, [fetchHistory])

  const checkIn = async () => {
    if (!navigator.geolocation) return alert('GPS not available')
    navigator.geolocation.getCurrentPosition(async (pos) => {
      try {
        await api.post('/attendance/check-in', {
          lat: pos.coords.latitude,
          lon: pos.coords.longitude,
          address: 'Lokasi anda',
          work_from: 'wfo',
        })
        fetchToday()
      } catch (err) { alert(err.response?.data?.message || 'Error') }
    })
  }

  const checkOut = async () => {
    if (!navigator.geolocation) return alert('GPS not available')
    navigator.geolocation.getCurrentPosition(async (pos) => {
      try {
        await api.post('/attendance/check-out', {
          lat: pos.coords.latitude,
          lon: pos.coords.longitude,
          address: 'Lokasi anda',
        })
        fetchToday()
      } catch (err) { alert(err.response?.data?.message || 'Error') }
    })
  }

  const submitCorrection = async () => {
    try {
      await api.post('/attendance/correction', { attendance_id: correctionModal, alasan })
      setCorrectionModal(null)
      setAlasan('')
      alert('Correction submitted')
    } catch (err) { alert(err.response?.data?.message || 'Error') }
  }

  const statusBadge = (s) => {
    const m = { hadir: 'bg-green-100 text-green-700', terlambat: 'bg-yellow-100 text-yellow-700', alpha: 'bg-red-100 text-red-700', izin: 'bg-blue-100 text-blue-700', sakit: 'bg-purple-100 text-purple-700', cuti: 'bg-indigo-100 text-indigo-700' }
    return <span className={`text-xs px-2 py-0.5 rounded-full ${m[s] || 'bg-gray-100 text-gray-700'}`}>{s}</span>
  }

  if (loading) return <div className="flex justify-center items-center min-h-screen"><div className="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-500" /></div>

  return (
    <div className="min-h-screen bg-gray-50">
      <Navbar />
      <div className="max-w-2xl mx-auto px-4 py-8">
        <h1 className="text-2xl font-bold text-gray-800 mb-6">Absensi</h1>

        <div className="bg-white rounded-xl shadow-sm border p-6 mb-6 text-center">
          {state?.checked_in ? (
            <>
              {state.attendance && (
                <div className="mb-4">
                  <p className="text-sm text-gray-500">Check-in: {state.attendance.check_in?.substring(0, 5)}</p>
                  <p className="text-sm text-gray-500">Status: {statusBadge(state.attendance.status)}</p>
                  {state.attendance.terlambat_menit > 0 && (
                    <div className="mt-2">
                      <p className="text-sm text-red-500">Terlambat {state.attendance.terlambat_menit} menit</p>
                      <button onClick={() => setCorrectionModal(state.attendance.id)} className="text-xs text-indigo-600 hover:underline">Ajukan konfirmasi keterlambatan</button>
                    </div>
                  )}
                </div>
              )}
              {!state.checked_out ? (
                <button onClick={checkOut} className="bg-red-500 text-white px-8 py-3 rounded-xl text-lg font-medium hover:bg-red-600 transition">Check-out</button>
              ) : (
                <p className="text-green-600 font-medium">Selesai — {state.attendance?.check_out?.substring(0, 5)}</p>
              )}
            </>
          ) : (
            <button onClick={checkIn} className="bg-indigo-600 text-white px-8 py-3 rounded-xl text-lg font-medium hover:bg-indigo-700 transition">Check-in</button>
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
                  {statusBadge(h.status)}
                  <span className="text-gray-500">{h.check_in?.substring(0, 5) || '-'} - {h.check_out?.substring(0, 5) || '-'}</span>
                </div>
              ))}
            </div>
          )}
        </div>
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
    </div>
  )
}

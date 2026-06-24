import { useState, useEffect, useCallback } from 'react'
import { useNavigate } from 'react-router-dom'
import api from '../api/axios'
import CameraModal from '../components/CameraModal'
import { ArrowLeft, Calendar, MapPinned, CheckCircle, XCircle, Clock, Camera, History } from 'lucide-react'

export default function AttendancePage() {
  const navigate = useNavigate()
  const [today, setToday] = useState(null)
  const [history, setHistory] = useState([])
  const [loading, setLoading] = useState(false)
  const [showCamera, setShowCamera] = useState(false)
  const [action, setAction] = useState(null)
  const [time, setTime] = useState(new Date())
  const [error, setError] = useState('')

  useEffect(() => {
    const timer = setInterval(() => setTime(new Date()), 1000)
    api.get('/attendance/today').then((r) => setToday(r.data)).catch(() => {})
    api.get('/attendance/history').then((r) => setHistory(r.data || [])).catch(() => {})
    return () => clearInterval(timer)
  }, [])

  const timeStr = time.toLocaleTimeString('id', { hour: '2-digit', minute: '2-digit', second: '2-digit' })
  const dateStr = time.toLocaleDateString('id', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })

  const handleAction = useCallback((type) => {
    if (!today?.has_schedule) { setError('Anda tidak memiliki jadwal hari ini'); return }
    if (type === 'check-in' && today?.checked_in) { setError('Anda sudah check-in'); return }
    if (type === 'check-out' && !today?.checked_in) { setError('Anda belum check-in'); return }
    if (type === 'check-out' && today?.checked_out) { setError('Anda sudah check-out'); return }
    setAction(type)
    setShowCamera(true)
  }, [today])

  const handleCameraConfirm = useCallback(async (foto) => {
    setShowCamera(false)
    setLoading(true)
    setError('')

    try {
      const pos = await new Promise((res, rej) => {
        navigator.geolocation.getCurrentPosition(
          (p) => res({ lat: p.coords.latitude, lng: p.coords.longitude }),
          () => res({ lat: 0, lng: 0 }),
          { timeout: 5000 }
        )
      })

      const endpoint = action === 'check-in' ? '/attendance/check-in' : '/attendance/check-out'
      const res = await api.post(endpoint, { ...pos, foto })
      setToday(res.data)
      api.get('/attendance/history').then((r) => setHistory(r.data || [])).catch(() => {})
    } catch (err) {
      setError(err.response?.data?.message || 'Gagal memproses absensi')
    } finally {
      setLoading(false)
      setAction(null)
    }
  }, [action])

  const statusBadge = (s) => {
    const map = { hadir: 'bg-green-100 text-green-700', terlambat: 'bg-yellow-100 text-yellow-700', alpha: 'bg-red-100 text-red-700', izin: 'bg-blue-100 text-blue-700', sakit: 'bg-purple-100 text-purple-700', cuti: 'bg-indigo-100 text-indigo-700' }
    return map[s] || 'bg-gray-100 text-gray-700'
  }

  const checkedIn = today?.checked_in
  const checkedOut = today?.checked_out

  return (
    <>
      <div className="-mx-4 -mt-4">
        <div className="bg-gradient-to-b from-primary-600 to-secondary-500 px-4 pt-4 pb-24 rounded-b-[32px] shadow-lg">
          <div className="flex items-center justify-between mb-6">
            <button onClick={() => navigate(-1)} className="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center text-white">
              <ArrowLeft size={20} />
            </button>
            <h1 className="text-white font-semibold text-base">Absensi</h1>
            <button className="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center text-white">
              <Calendar size={18} />
            </button>
          </div>

          <div className="text-center text-white">
            <p className="text-sm text-white/70 mb-1">{dateStr}</p>
            <p className="text-4xl font-bold tracking-tight mb-3">{timeStr}</p>
            <span className={`inline-flex items-center gap-1.5 text-sm font-medium px-3 py-1.5 rounded-full ${checkedIn ? 'bg-green-500/20 text-green-100' : 'bg-white/20 text-white/80'}`}>
              <span className={`w-2 h-2 rounded-full ${checkedIn ? 'bg-green-400 animate-pulse' : 'bg-white/50'}`} />
              {checkedIn ? (checkedOut ? 'Selesai' : 'Sedang Bekerja') : 'Belum Absen'}
            </span>
          </div>
        </div>

        <div className="px-4 -mt-16 relative z-10 space-y-4">
          {error && (
            <div className="bg-red-50 border border-red-100 text-red-600 text-sm px-4 py-3 rounded-2xl">
              {error}
            </div>
          )}

          <div className="card p-5">
            <div className="flex items-center gap-2 mb-3">
              <MapPinned size={16} className="text-primary-600" />
              <p className="text-sm font-semibold text-gray-800">Lokasi Kerja</p>
            </div>
            <p className="text-sm text-gray-600 font-medium">Kantor Pusat</p>
            <p className="text-xs text-gray-400 mb-3">Jl. Contoh No. 123, Jakarta Selatan</p>
            <div className="bg-gray-100 rounded-xl h-28 flex items-center justify-center text-gray-400 text-xs gap-2">
              <MapPinned size={16} />
              Peta Lokasi
            </div>
            <div className="flex items-center gap-2 mt-3 text-xs text-green-600 bg-green-50 rounded-lg px-3 py-2">
              <span className="w-1.5 h-1.5 rounded-full bg-green-500" />
              Dalam area geofence
            </div>
          </div>

          <div className="card p-5">
            <p className="text-sm font-semibold text-gray-800 mb-3">Status Absensi</p>
            <div className="flex gap-3">
              <div className="flex-1 bg-gray-50 rounded-xl p-4 text-center">
                <CheckCircle size={20} className={`mx-auto mb-1.5 ${checkedIn ? 'text-green-500' : 'text-gray-300'}`} />
                <p className="text-[11px] text-gray-400 font-medium">Check In</p>
                <p className="text-sm font-bold text-gray-700 mt-0.5">
                  {checkedIn ? today.attendance?.check_in?.substring(0, 5) : '--:--'}
                </p>
              </div>
              <div className="flex-1 bg-gray-50 rounded-xl p-4 text-center">
                <XCircle size={20} className={`mx-auto mb-1.5 ${checkedOut ? 'text-red-500' : 'text-gray-300'}`} />
                <p className="text-[11px] text-gray-400 font-medium">Check Out</p>
                <p className="text-sm font-bold text-gray-700 mt-0.5">
                  {checkedOut ? today.attendance?.check_out?.substring(0, 5) : '--:--'}
                </p>
              </div>
            </div>
          </div>

          {!checkedIn ? (
            <button onClick={() => handleAction('check-in')} disabled={loading}
              className="btn-gradient w-full h-14 text-base flex items-center justify-center gap-2 disabled:opacity-60">
              <Camera size={20} /> {loading ? 'Memproses...' : 'Check In'}
            </button>
          ) : !checkedOut ? (
            <button onClick={() => handleAction('check-out')} disabled={loading}
              className="btn-gradient w-full h-14 text-base flex items-center justify-center gap-2 disabled:opacity-60">
              <Clock size={20} /> {loading ? 'Memproses...' : 'Check Out'}
            </button>
          ) : null}

          <div className="card p-5">
            <div className="flex items-center justify-between mb-3">
              <div className="flex items-center gap-2">
                <History size={16} className="text-gray-400" />
                <p className="text-sm font-semibold text-gray-800">Riwayat</p>
              </div>
              <span className="text-[11px] text-gray-400">{history.length} data</span>
            </div>
            <div className="space-y-2">
              {history.length === 0 && <p className="text-xs text-gray-400 text-center py-4">Belum ada riwayat absensi</p>}
              {history.slice(0, 5).map((h) => (
                <div key={h.id} className="flex items-center justify-between py-1.5 border-b border-gray-50 last:border-0">
                  <div>
                    <p className="text-xs font-medium text-gray-700">{new Date(h.tanggal).toLocaleDateString('id', { weekday: 'short', day: 'numeric', month: 'short' })}</p>
                    <p className="text-[11px] text-gray-400">{h.check_in?.substring(0, 5) || '--:--'} - {h.check_out?.substring(0, 5) || '--:--'}</p>
                  </div>
                  <span className={`text-[10px] font-medium px-2 py-0.5 rounded-full ${statusBadge(h.status)}`}>{h.status}</span>
                </div>
              ))}
            </div>
          </div>
        </div>
      </div>

      {showCamera && <CameraModal onConfirm={handleCameraConfirm} onClose={() => { setShowCamera(false); setAction(null) }} />}
    </>
  )
}

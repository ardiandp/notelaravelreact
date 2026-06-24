import { useState, useEffect } from 'react'
import api from '../api/axios'
import { ClipboardList, Paperclip, CalendarDays, X } from 'lucide-react'

export default function LeaveRequestsPage() {
  const [leaveTypes, setLeaveTypes] = useState([])
  const [balances, setBalances] = useState([])
  const [requests, setRequests] = useState([])
  const [loading, setLoading] = useState(false)
  const [message, setMessage] = useState('')
  const [messageType, setMessageType] = useState('')

  const [form, setForm] = useState({
    leave_type_id: '',
    start_date: '',
    end_date: '',
    reason: '',
    file: null,
  })

  useEffect(() => {
    api.get('/leave-types').then((r) => setLeaveTypes(r.data)).catch(() => {})
    api.get('/leave-balances').then((r) => setBalances(r.data)).catch(() => {})
    api.get('/leave-requests').then((r) => setRequests(r.data)).catch(() => {})
  }, [])

  const totalDays = form.start_date && form.end_date
    ? Math.max(1, Math.ceil((new Date(form.end_date) - new Date(form.start_date)) / (1000 * 60 * 60 * 24)) + 1)
    : 0

  const handleSubmit = async (e) => {
    e.preventDefault()
    if (!form.leave_type_id || !form.start_date || !form.end_date) {
      setMessage('Lengkapi semua field'); setMessageType('error'); return
    }
    setLoading(true)
    setMessage('')
    try {
      const fd = new FormData()
      fd.append('leave_type_id', form.leave_type_id)
      fd.append('start_date', form.start_date)
      fd.append('end_date', form.end_date)
      fd.append('reason', form.reason)
      if (form.file) fd.append('file', form.file)

      await api.post('/leave-requests', fd, { headers: { 'Content-Type': 'multipart/form-data' } })
      setMessage('Pengajuan berhasil dikirim'); setMessageType('success')
      setForm({ leave_type_id: '', start_date: '', end_date: '', reason: '', file: null })
      api.get('/leave-requests').then((r) => setRequests(r.data)).catch(() => {})
      api.get('/leave-balances').then((r) => setBalances(r.data)).catch(() => {})
    } catch (err) {
      setMessage(err.response?.data?.message || 'Gagal mengirim pengajuan')
      setMessageType('error')
    } finally {
      setLoading(false)
    }
  }

  const statusBadge = (s) => {
    const map = { pending: 'bg-yellow-100 text-yellow-700', approved: 'bg-green-100 text-green-700', rejected: 'bg-red-100 text-red-700', cancelled: 'bg-gray-100 text-gray-500' }
    return map[s] || 'bg-gray-100 text-gray-700'
  }

  return (
    <div>
      {balances.length > 0 && (
        <div className="flex gap-2 overflow-x-auto pb-2 mb-5 scrollbar-hide">
          {balances.map((b) => (
            <div key={b.id} className="card p-3 min-w-[120px] shrink-0 text-center">
              <p className="text-xs text-gray-400">{b.leave_type?.nama || 'Cuti'}</p>
              <p className="text-xl font-bold text-primary-600">{b.kuota - b.terpakai}<span className="text-xs text-gray-400 font-normal">/{b.kuota}</span></p>
            </div>
          ))}
        </div>
      )}

      <div className="card p-5 mb-5">
        <div className="flex items-center gap-2 mb-4">
          <ClipboardList size={18} className="text-primary-600" />
          <p className="text-sm font-semibold text-gray-800">Pengajuan Baru</p>
        </div>

        {message && (
          <div className={`text-sm px-4 py-3 rounded-xl mb-4 ${messageType === 'success' ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600'}`}>
            {message}
          </div>
        )}

        <form onSubmit={handleSubmit} className="space-y-4">
          <div>
            <label className="text-xs font-medium text-gray-500 mb-1.5 block">Jenis Cuti / Izin</label>
            <select value={form.leave_type_id} onChange={(e) => setForm({ ...form, leave_type_id: e.target.value })}
              className="w-full h-12 px-4 rounded-2xl border border-gray-200 bg-gray-50 text-sm text-gray-800 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition appearance-none">
              <option value="">Pilih jenis</option>
              {leaveTypes.map((t) => (
                <option key={t.id} value={t.id}>{t.nama}</option>
              ))}
            </select>
          </div>

          <div className="flex gap-3">
            <div className="flex-1">
              <label className="text-xs font-medium text-gray-500 mb-1.5 block">Tanggal Mulai</label>
              <div className="relative">
                <CalendarDays size={16} className="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400" />
                <input type="date" value={form.start_date} onChange={(e) => setForm({ ...form, start_date: e.target.value })}
                  className="w-full h-12 pl-9 pr-4 rounded-2xl border border-gray-200 bg-gray-50 text-sm text-gray-800 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition" />
              </div>
            </div>
            <div className="flex-1">
              <label className="text-xs font-medium text-gray-500 mb-1.5 block">Tanggal Akhir</label>
              <div className="relative">
                <CalendarDays size={16} className="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400" />
                <input type="date" value={form.end_date} onChange={(e) => setForm({ ...form, end_date: e.target.value })}
                  className="w-full h-12 pl-9 pr-4 rounded-2xl border border-gray-200 bg-gray-50 text-sm text-gray-800 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition" />
              </div>
            </div>
          </div>

          {totalDays > 0 && (
            <div className="bg-primary-50 rounded-xl px-4 py-2.5 text-sm text-primary-700 font-medium">
              Total: {totalDays} hari
            </div>
          )}

          <div>
            <label className="text-xs font-medium text-gray-500 mb-1.5 block">Alasan</label>
            <textarea value={form.reason} onChange={(e) => setForm({ ...form, reason: e.target.value })} rows={3}
              placeholder="Tulis alasan pengajuan..."
              className="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-sm text-gray-800 placeholder:text-gray-400 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition resize-none" />
          </div>

          <div>
            <label className="text-xs font-medium text-gray-500 mb-1.5 block">Lampiran <span className="text-gray-300">(opsional)</span></label>
            <label className="flex items-center gap-3 w-full h-14 px-4 rounded-2xl border-2 border-dashed border-gray-200 bg-gray-50 text-sm text-gray-500 cursor-pointer hover:border-primary-300 hover:bg-primary-50/50 transition">
              <Paperclip size={18} className="text-gray-400" />
              {form.file ? form.file.name : 'Upload file pendukung'}
              <input type="file" className="hidden" onChange={(e) => setForm({ ...form, file: e.target.files[0] || null })} />
            </label>
          </div>

          <button type="submit" disabled={loading}
            className="btn-gradient w-full h-13 text-sm disabled:opacity-60">
            {loading ? 'Mengirim...' : 'Ajukan'}
          </button>
        </form>
      </div>

      <div className="card p-5">
        <div className="flex items-center justify-between mb-3">
          <p className="text-sm font-semibold text-gray-800">Riwayat Pengajuan</p>
        </div>
        <div className="space-y-2">
          {requests.length === 0 && <p className="text-xs text-gray-400 text-center py-4">Belum ada pengajuan</p>}
          {requests.map((r) => (
            <div key={r.id} className="flex items-center justify-between py-2.5 border-b border-gray-50 last:border-0">
              <div>
                <p className="text-sm font-medium text-gray-700">{r.leave_type?.nama}</p>
                <p className="text-[11px] text-gray-400">{r.start_date} - {r.end_date}</p>
              </div>
              <span className={`text-[10px] font-medium px-2 py-0.5 rounded-full ${statusBadge(r.status)}`}>{r.status}</span>
            </div>
          ))}
        </div>
      </div>
    </div>
  )
}

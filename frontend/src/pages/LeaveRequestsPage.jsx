import { useState, useEffect, useCallback } from 'react'
import api from '../api/axios'

export default function LeaveRequestsPage() {
  const [requests, setRequests] = useState([])
  const [leaveTypes, setLeaveTypes] = useState([])
  const [balances, setBalances] = useState([])
  const [showForm, setShowForm] = useState(false)
  const [form, setForm] = useState({ leave_type_id: '', tanggal_mulai: '', tanggal_selesai: '', keterangan: '' })

  const fetchData = useCallback(async () => {
    try {
      const [reqRes, typeRes, balRes] = await Promise.all([
        api.get('/leave-requests'),
        api.get('/leave-types'),
        api.get('/leave-balances'),
      ])
      setRequests(reqRes.data)
      setLeaveTypes(typeRes.data)
      setBalances(balRes.data)
    } catch {}
  }, [])

  useEffect(() => { fetchData() }, [fetchData])

  const submit = async (e) => {
    e.preventDefault()
    try {
      await api.post('/leave-requests', form)
      setShowForm(false)
      setForm({ leave_type_id: '', tanggal_mulai: '', tanggal_selesai: '', keterangan: '' })
      fetchData()
    } catch (err) { alert(Object.values(err.response?.data?.errors || {}).flat().join(', ') || err.response?.data?.message) }
  }

  const cancel = async (id) => {
    try {
      await api.put(`/leave-requests/${id}/cancel`)
      fetchData()
    } catch (err) { alert(err.response?.data?.message) }
  }

  const statusBadge = (s) => {
    const m = { pending: 'bg-yellow-100 text-yellow-700', approved: 'bg-green-100 text-green-700', rejected: 'bg-red-100 text-red-700', cancelled: 'bg-gray-100 text-gray-500' }
    return <span className={`text-xs px-2 py-0.5 rounded-full ${m[s] || ''}`}>{s}</span>
  }

  return (
    <div>
      <div className="flex justify-between items-center mb-6">
        <button onClick={() => setShowForm(!showForm)} className="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium">{showForm ? 'Batal' : '+ Ajukan'}</button>
      </div>

      {balances.length > 0 && (
        <div className="grid grid-cols-2 gap-3 mb-6">
          {balances.map((b) => (
            <div key={b.id} className="bg-green-50 border border-green-200 rounded-xl shadow-sm p-4 text-center">
              <p className="text-sm text-gray-500">{b.leave_type?.nama || 'Cuti'}</p>
              <p className="text-2xl font-bold text-indigo-600">{b.kuota - b.terpakai}<span className="text-sm text-gray-400">/{b.kuota}</span></p>
            </div>
          ))}
        </div>
      )}

      {showForm && (
        <form onSubmit={submit} className="bg-white rounded-xl shadow-sm border p-6 mb-6 space-y-4">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Jenis</label>
            <select value={form.leave_type_id} onChange={(e) => setForm({ ...form, leave_type_id: e.target.value })} className="w-full border rounded-lg px-4 py-2.5" required>
              <option value="">Pilih</option>
              {leaveTypes.map((t) => <option key={t.id} value={t.id}>{t.nama}</option>)}
            </select>
          </div>
          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Mulai</label>
              <input type="date" value={form.tanggal_mulai} onChange={(e) => setForm({ ...form, tanggal_mulai: e.target.value })} className="w-full border rounded-lg px-4 py-2.5" required />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Selesai</label>
              <input type="date" value={form.tanggal_selesai} onChange={(e) => setForm({ ...form, tanggal_selesai: e.target.value })} className="w-full border rounded-lg px-4 py-2.5" required />
            </div>
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
            <textarea value={form.keterangan} onChange={(e) => setForm({ ...form, keterangan: e.target.value })} className="w-full border rounded-lg px-4 py-2.5" rows={3} required />
          </div>
          <button type="submit" className="w-full bg-indigo-600 text-white py-2.5 rounded-lg font-medium">Ajukan</button>
        </form>
      )}

      <div className="bg-white rounded-xl shadow-sm border p-6">
        <h2 className="font-semibold mb-4">Riwayat Pengajuan</h2>
        {requests.length === 0 ? <p className="text-gray-400 text-center py-4">Belum ada pengajuan</p> : (
          <div className="space-y-3">
            {requests.map((r) => (
              <div key={r.id} className="border rounded-lg p-4">
                <div className="flex justify-between items-start mb-2">
                  <div>
                    <p className="font-medium text-sm">{r.leave_type?.nama}</p>
                    <p className="text-xs text-gray-500">{r.tanggal_mulai} s/d {r.tanggal_selesai} ({r.jumlah_hari} hari)</p>
                  </div>
                  {statusBadge(r.status)}
                </div>
                <p className="text-sm text-gray-600">{r.keterangan}</p>
                {r.status === 'pending' && (
                  <button onClick={() => cancel(r.id)} className="text-xs text-red-500 hover:underline mt-2">Batalkan</button>
                )}
                {r.approvals?.length > 0 && (
                  <div className="mt-2 flex gap-2 text-xs text-gray-400">
                    {r.approvals.map((a) => (
                      <span key={a.id} className={`${a.status === 'approved' ? 'text-green-500' : a.status === 'rejected' ? 'text-red-500' : ''}`}>
                        {a.approver?.name}: {a.status}
                      </span>
                    ))}
                  </div>
                )}
              </div>
            ))}
          </div>
        )}
      </div>
    </div>
  )
}

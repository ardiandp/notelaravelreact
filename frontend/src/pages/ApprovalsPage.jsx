import { useState, useEffect, useCallback } from 'react'
import api from '../api/axios'
import { CheckCircle, XCircle, MessageSquare } from 'lucide-react'

export default function ApprovalsPage() {
  const [approvals, setApprovals] = useState([])
  const [catatan, setCatatan] = useState({})

  const fetchApprovals = useCallback(async () => {
    try {
      const res = await api.get('/approvals/pending')
      setApprovals(res.data)
    } catch {}
  }, [])

  useEffect(() => { fetchApprovals() }, [fetchApprovals])

  const handleApprove = async (id) => {
    try {
      await api.post(`/approvals/${id}/approve`, { catatan: catatan[id] || '' })
      fetchApprovals()
    } catch (err) { alert(err.response?.data?.message) }
  }

  const handleReject = async (id) => {
    try {
      await api.post(`/approvals/${id}/reject`, { catatan: catatan[id] || '' })
      fetchApprovals()
    } catch (err) { alert(err.response?.data?.message) }
  }

  return (
    <div>
      {approvals.length === 0 ? (
        <div className="card p-8 text-center">
          <CheckCircle size={40} className="mx-auto text-green-400 mb-3" />
          <p className="text-sm text-gray-500">Tidak ada persetujuan pending</p>
        </div>
      ) : (
        <div className="space-y-4">
          {approvals.map((a) => {
            const req = a.requestable
            return (
              <div key={a.id} className="card p-5">
                <div className="flex items-center gap-3 mb-3">
                  <div className="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 font-bold text-sm">
                    {req?.user?.name?.charAt(0)?.toUpperCase() || '?'}
                  </div>
                  <div>
                    <p className="text-sm font-semibold text-gray-800">{req?.user?.name || '-'}</p>
                    <p className="text-[11px] text-gray-400">{req?.leave_type?.nama || '-'} &middot; Step {a.step_order}</p>
                  </div>
                </div>
                <p className="text-xs text-gray-400 mb-2">{req?.tanggal_mulai} s/d {req?.tanggal_selesai} ({req?.jumlah_hari} hari)</p>
                {req?.keterangan && (
                  <p className="text-sm text-gray-600 bg-gray-50 rounded-xl p-3 mb-3">{req.keterangan}</p>
                )}
                <div className="relative mb-3">
                  <MessageSquare size={14} className="absolute left-3 top-3 text-gray-400" />
                  <textarea
                    placeholder="Catatan (opsional)"
                    value={catatan[a.id] || ''}
                    onChange={(e) => setCatatan({ ...catatan, [a.id]: e.target.value })}
                    className="w-full pl-9 pr-3 py-2.5 rounded-2xl border border-gray-200 bg-gray-50 text-sm text-gray-800 placeholder:text-gray-400 focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition resize-none"
                    rows={2}
                  />
                </div>
                <div className="flex gap-2">
                  <button onClick={() => handleApprove(a.id)} className="flex-1 flex items-center justify-center gap-1.5 bg-green-600 text-white h-11 rounded-2xl text-sm font-medium hover:bg-green-700 transition">
                    <CheckCircle size={16} /> Setujui
                  </button>
                  <button onClick={() => handleReject(a.id)} className="flex-1 flex items-center justify-center gap-1.5 bg-red-500 text-white h-11 rounded-2xl text-sm font-medium hover:bg-red-600 transition">
                    <XCircle size={16} /> Tolak
                  </button>
                </div>
              </div>
            )
          })}
        </div>
      )}
    </div>
  )
}

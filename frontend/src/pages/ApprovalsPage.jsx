import { useState, useEffect, useCallback } from 'react'
import api from '../api/axios'

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
        <div className="bg-amber-50 border border-amber-200 rounded-xl shadow-sm p-8 text-center text-gray-500">Tidak ada persetujuan pending</div>
      ) : (
        <div className="space-y-4">
          {approvals.map((a) => {
            const req = a.requestable
            return (
              <div key={a.id} className="bg-white rounded-xl shadow-sm border p-6">
                <div className="mb-4">
                  <p className="font-medium">{req?.user?.name || '-'}</p>
                  <p className="text-sm text-gray-500">{req?.leave_type?.nama || '-'} — Step {a.step_order}</p>
                  <p className="text-xs text-gray-400">{req?.tanggal_mulai} s/d {req?.tanggal_selesai} ({req?.jumlah_hari} hari)</p>
                  <p className="text-sm text-gray-600 mt-2">{req?.keterangan}</p>
                </div>
                <textarea
                  placeholder="Catatan (opsional)"
                  value={catatan[a.id] || ''}
                  onChange={(e) => setCatatan({ ...catatan, [a.id]: e.target.value })}
                  className="w-full border rounded-lg p-3 text-sm mb-3"
                  rows={2}
                />
                <div className="flex gap-2">
                  <button onClick={() => handleApprove(a.id)} className="bg-green-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-green-700">Setujui</button>
                  <button onClick={() => handleReject(a.id)} className="bg-red-500 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-red-600">Tolak</button>
                </div>
              </div>
            )
          })}
        </div>
      )}
    </div>
  )
}

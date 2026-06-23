import { useState, useEffect } from 'react'
import { Link } from 'react-router-dom'
import api from '../api/axios'
import Navbar from '../components/Navbar'
import { useAuth } from '../context/AuthContext'

export default function Dashboard() {
  const { user } = useAuth()
  const [today, setToday] = useState(null)
  const [balances, setBalances] = useState([])
  const [approvals, setApprovals] = useState(0)

  useEffect(() => {
    api.get('/attendance/today').then((res) => setToday(res.data)).catch(() => {})
    api.get('/leave-balances').then((res) => setBalances(res.data)).catch(() => {})
    api.get('/approvals/pending').then((res) => setApprovals(res.data.length)).catch(() => {})
  }, [])

  return (
    <div className="min-h-screen bg-gray-50">
      <Navbar />
      <div className="max-w-4xl mx-auto px-4 py-8">
        <h1 className="text-2xl font-bold text-gray-800 mb-2">Selamat datang, {user?.name}</h1>
        <p className="text-gray-500 mb-6">{new Date().toLocaleDateString('id', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })}</p>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
          <Link to="/attendance" className="bg-white rounded-xl shadow-sm border p-5 hover:shadow-md transition">
            <div className="flex items-center justify-between mb-3">
              <p className="text-sm text-gray-500 font-medium">Absensi Hari Ini</p>
              <span className="text-2xl">{today?.checked_in ? '✅' : '⏳'}</span>
            </div>
            {today?.checked_in ? (
              <p className="text-sm">{today?.checked_out ? `Selesai ${today.attendance?.check_out?.substring(0, 5)}` : 'Belum check-out'}</p>
            ) : (
              <p className="text-sm text-indigo-600 font-medium">Belum absen</p>
            )}
          </Link>

          <Link to="/leave-requests" className="bg-white rounded-xl shadow-sm border p-5 hover:shadow-md transition">
            <div className="flex items-center justify-between mb-3">
              <p className="text-sm text-gray-500 font-medium">Sisa Cuti</p>
              <span className="text-2xl">📋</span>
            </div>
            {balances.map((b) => (
              <p key={b.id} className="text-sm">{b.leave_type?.nama}: <strong>{b.kuota - b.terpakai}/{b.kuota}</strong></p>
            ))}
            {balances.length === 0 && <p className="text-sm text-gray-400">Tidak ada data</p>}
          </Link>

          <Link to="/approvals" className="bg-white rounded-xl shadow-sm border p-5 hover:shadow-md transition">
            <div className="flex items-center justify-between mb-3">
              <p className="text-sm text-gray-500 font-medium">Persetujuan</p>
              <span className="text-2xl">📌</span>
            </div>
            {approvals > 0 ? (
              <p className="text-sm text-red-500 font-medium">{approvals} perlu disetujui</p>
            ) : (
              <p className="text-sm text-gray-400">Tidak ada</p>
            )}
          </Link>
        </div>
      </div>
    </div>
  )
}

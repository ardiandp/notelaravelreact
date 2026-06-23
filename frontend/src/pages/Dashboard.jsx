import { useState, useEffect } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import api from '../api/axios'
import { useAuth } from '../context/AuthContext'

export default function Dashboard() {
  const { user } = useAuth()
  const navigate = useNavigate()
  const [today, setToday] = useState(null)
  const [balances, setBalances] = useState([])
  const [approvals, setApprovals] = useState(0)

  useEffect(() => {
    api.get('/attendance/today').then((res) => setToday(res.data)).catch(() => {})
    api.get('/leave-balances').then((res) => setBalances(res.data)).catch(() => {})
    api.get('/approvals/pending').then((res) => setApprovals(res.data.length)).catch(() => {})
  }, [])

  const cards = [
    {
      to: '/attendance',
      title: 'Absensi Hari Ini',
      icon: '📍',
      bg: 'bg-blue-50 border-blue-200',
      hover: 'hover:bg-blue-100',
      content: today?.checked_in
        ? <p className="text-sm">{today?.checked_out ? `Selesai ${today.attendance?.check_out?.substring(0, 5)}` : 'Belum check-out'}</p>
        : <p className="text-sm text-blue-600 font-medium">Belum absen</p>,
    },
    {
      to: '/leave-requests',
      title: 'Sisa Cuti',
      icon: '✈️',
      bg: 'bg-green-50 border-green-200',
      hover: 'hover:bg-green-100',
      content: balances.length > 0
        ? balances.map((b) => (
            <p key={b.id} className="text-sm">{b.leave_type?.nama}: <strong>{b.kuota - b.terpakai}/{b.kuota}</strong></p>
          ))
        : <p className="text-sm text-gray-400">Tidak ada data</p>,
    },
    {
      to: '/approvals',
      title: 'Persetujuan',
      icon: '✅',
      bg: 'bg-amber-50 border-amber-200',
      hover: 'hover:bg-amber-100',
      content: approvals > 0
        ? <p className="text-sm text-red-500 font-medium">{approvals} perlu disetujui</p>
        : <p className="text-sm text-gray-400">Tidak ada</p>,
    },
  ]

  return (
    <div>
      <h1 className="text-2xl font-bold text-gray-800 mb-2">Selamat datang, {user?.name}</h1>
      <p className="text-gray-500 mb-6">{new Date().toLocaleDateString('id', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })}</p>

      <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        {cards.map((c) => (
          <Link key={c.to} to={c.to} className={`rounded-xl shadow-sm border p-5 transition ${c.bg} ${c.hover}`}>
            <div className="flex items-center justify-between mb-3">
              <p className="text-sm text-gray-500 font-medium">{c.title}</p>
              <span className="text-2xl">{c.icon}</span>
            </div>
            {c.content}
          </Link>
        ))}
      </div>

      <div onClick={() => navigate('/schedule')} className="rounded-xl shadow-sm border border-indigo-200 bg-indigo-50 p-5 cursor-pointer hover:bg-indigo-100 transition mb-8">
        <div className="flex items-center justify-between mb-2">
          <p className="text-sm text-gray-500 font-medium">Jadwal Hari Ini</p>
          <span className="text-2xl">📅</span>
        </div>
        {today?.has_schedule && today?.schedule ? (
          <div className="space-y-1">
            <div className="flex items-center gap-2">
              <span className="inline-block text-xs font-medium px-2 py-0.5 rounded bg-indigo-100 text-indigo-700">{today.schedule.nama}</span>
              <span className="text-xs text-gray-500">{today.schedule.jam_masuk} - {today.schedule.jam_pulang}</span>
              <span className={`inline-block text-xs px-1.5 py-0.5 rounded ${today.work_from === 'wfo' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'}`}>
                {today.work_from === 'wfo' ? 'WFO' : 'WFA'}
              </span>
            </div>
            <p className="text-xs text-indigo-600 font-medium mt-2">Lihat kalender &rarr;</p>
          </div>
        ) : (
          <p className="text-sm text-gray-400">Tidak ada jadwal hari ini</p>
        )}
      </div>
    </div>
  )
}

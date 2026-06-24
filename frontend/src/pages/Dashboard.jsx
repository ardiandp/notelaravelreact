import { useState, useEffect } from 'react'
import { useNavigate } from 'react-router-dom'
import api from '../api/axios'
import { useAuth } from '../context/AuthContext'
import {
  Bell,
  MapPin,
  Plane,
  FileText,
  Clock,
  ClipboardList,
  Megaphone,
  Wallet,
  User,
  Grid,
  ChevronRight,
  Calendar,
  MapPinned,
} from 'lucide-react'

const quickMenu = [
  { to: '/attendance', label: 'Absensi', icon: MapPin, color: 'bg-blue-100 text-blue-600' },
  { to: '/leave-requests', label: 'Cuti', icon: Plane, color: 'bg-emerald-100 text-emerald-600' },
  { to: '/leave-requests', label: 'Izin', icon: FileText, color: 'bg-amber-100 text-amber-600' },
  { to: '#', label: 'Lembur', icon: Clock, color: 'bg-purple-100 text-purple-600' },
  { to: '/leave-requests', label: 'Pengajuan', icon: ClipboardList, color: 'bg-cyan-100 text-cyan-600' },
  { to: '#', label: 'Pengumuman', icon: Megaphone, color: 'bg-rose-100 text-rose-600' },
  { to: '#', label: 'Slip Gaji', icon: Wallet, color: 'bg-orange-100 text-orange-600' },
  { to: '/profile', label: 'Profil', icon: User, color: 'bg-sky-100 text-sky-600' },
  { to: '#', label: 'Lainnya', icon: Grid, color: 'bg-gray-100 text-gray-600' },
]

const announcements = [
  { title: 'Libur Nasional', desc: 'Libur Hari Raya Idul Adha', date: '17 Jun' },
  { title: 'Penggajian', desc: 'Penggajian bulan Juni akan diproses', date: '25 Jun' },
]

const events = [
  { title: 'Rapat Bulanan', location: 'Ruang Serbaguna Lantai 3', day: '28', month: 'Jun' },
]

export default function Dashboard() {
  const { user } = useAuth()
  const navigate = useNavigate()
  const [today, setToday] = useState(null)
  const [balances, setBalances] = useState([])
  const [approvals, setApprovals] = useState(0)
  const [time, setTime] = useState(new Date())

  useEffect(() => {
    const timer = setInterval(() => setTime(new Date()), 1000)
    api.get('/attendance/today').then((res) => setToday(res.data)).catch(() => {})
    api.get('/leave-balances').then((res) => setBalances(res.data)).catch(() => {})
    api.get('/approvals/pending').then((res) => setApprovals(res.data.length)).catch(() => {})
    return () => clearInterval(timer)
  }, [])

  const dateStr = time.toLocaleDateString('id', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })
  const timeStr = time.toLocaleTimeString('id', { hour: '2-digit', minute: '2-digit', second: '2-digit' })
  const greeting = time.getHours() < 12 ? 'Selamat Pagi' : time.getHours() < 18 ? 'Selamat Siang' : 'Selamat Malam'

  return (
    <div className="-mx-4 -mt-4">
      <div className="bg-gradient-to-b from-primary-600 to-secondary-500 px-4 pt-4 pb-20 rounded-b-[32px] shadow-lg">
        <div className="flex items-center justify-between mb-6">
          <div className="flex items-center gap-3">
            <div className="w-11 h-11 rounded-full bg-white/20 flex items-center justify-center text-white font-bold text-sm">
              {user?.name?.charAt(0)?.toUpperCase() || 'U'}
            </div>
            <div>
              <p className="text-white/80 text-xs">{greeting}</p>
              <p className="text-white font-semibold text-base">{user?.name || 'User'}</p>
            </div>
          </div>
          <div className="relative">
            <Bell size={22} className="text-white" />
            {approvals > 0 && (
              <span className="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-red-500 text-white text-[9px] font-bold flex items-center justify-center">
                {approvals > 9 ? '9+' : approvals}
              </span>
            )}
          </div>
        </div>
      </div>

      <div className="px-4 -mt-12 relative z-10">
        <div className="card p-5">
          <div className="flex items-center justify-between mb-3">
            <p className="text-xs text-gray-400 font-medium">{dateStr}</p>
            <span className="flex items-center gap-1.5 text-[11px] font-medium text-green-600 bg-green-50 px-2.5 py-1 rounded-full">
              <span className="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse" />
              {today?.checked_in ? 'Sudah Absen' : 'Belum Absen'}
            </span>
          </div>
          <p className="text-3xl font-bold text-gray-800 tracking-tight mb-4">{timeStr}</p>
          <div className="flex gap-4 text-sm">
            <div className="flex-1 bg-gray-50 rounded-xl p-3 text-center">
              <p className="text-[11px] text-gray-400 font-medium mb-0.5">Check In</p>
              <p className="font-semibold text-gray-700">
                {today?.checked_in ? today?.attendance?.check_in?.substring(0, 5) || '--:--' : '--:--'}
              </p>
            </div>
            <div className="flex-1 bg-gray-50 rounded-xl p-3 text-center">
              <p className="text-[11px] text-gray-400 font-medium mb-0.5">Check Out</p>
              <p className="font-semibold text-gray-700">
                {today?.checked_out ? today?.attendance?.check_out?.substring(0, 5) || '--:--' : '--:--'}
              </p>
            </div>
          </div>
        </div>

        <div className="mt-6">
          <p className="text-sm font-semibold text-gray-800 mb-3">Menu Cepat</p>
          <div className="grid grid-cols-3 gap-3">
            {quickMenu.map((m) => (
              <button
                key={m.label}
                onClick={() => (m.to === '#' ? alert('Fitur akan segera tersedia') : navigate(m.to))}
                className="card p-3 flex flex-col items-center gap-2 hover:scale-[1.02] transition"
              >
                <div className={`w-10 h-10 rounded-xl flex items-center justify-center ${m.color}`}>
                  <m.icon size={20} />
                </div>
                <span className="text-[11px] font-medium text-gray-600">{m.label}</span>
              </button>
            ))}
          </div>
        </div>

        <div className="mt-6">
          <div className="flex items-center justify-between mb-3">
            <p className="text-sm font-semibold text-gray-800">Pengumuman</p>
            <button className="text-[11px] text-primary-600 font-medium flex items-center gap-0.5">
              Lihat Semua <ChevronRight size={14} />
            </button>
          </div>
          <div className="space-y-2">
            {announcements.map((a) => (
              <div key={a.title} className="card p-4 flex items-center gap-3">
                <div className="w-10 h-10 rounded-xl bg-primary-50 flex items-center justify-center shrink-0">
                  <Megaphone size={18} className="text-primary-600" />
                </div>
                <div className="flex-1 min-w-0">
                  <p className="text-sm font-medium text-gray-800">{a.title}</p>
                  <p className="text-xs text-gray-400 truncate">{a.desc}</p>
                </div>
                <span className="text-[10px] text-gray-400 whitespace-nowrap">{a.date}</span>
              </div>
            ))}
          </div>
        </div>

        <div className="mt-6 mb-4">
          <p className="text-sm font-semibold text-gray-800 mb-3">Acara Mendatang</p>
          {events.map((e) => (
            <div key={e.title} className="card p-4 flex items-center gap-3">
              <div className="w-12 h-12 rounded-xl bg-gradient-to-b from-primary-600 to-secondary-600 flex flex-col items-center justify-center text-white shrink-0">
                <span className="text-[10px] font-medium leading-none">{e.month}</span>
                <span className="text-lg font-bold leading-none mt-0.5">{e.day}</span>
              </div>
              <div className="flex-1 min-w-0">
                <p className="text-sm font-medium text-gray-800">{e.title}</p>
                <p className="text-xs text-gray-400 flex items-center gap-1 mt-0.5">
                  <MapPinned size={12} /> {e.location}
                </p>
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  )
}

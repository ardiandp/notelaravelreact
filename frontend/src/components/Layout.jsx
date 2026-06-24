import { Link, useLocation } from 'react-router-dom'
import { useAuth } from '../context/AuthContext'
import { LayoutDashboard, CalendarDays, Bell, User, Fingerprint } from 'lucide-react'

const tabs = [
  { to: '/', label: 'Home', icon: LayoutDashboard },
  { to: '/schedule', label: 'Kalender', icon: CalendarDays },
  { to: '/attendance', label: 'Absen', icon: Fingerprint, fab: true },
  { to: '/approvals', label: 'Notif', icon: Bell },
  { to: '/profile', label: 'Profil', icon: User },
]

export default function Layout({ children, title, subtitle, noHeader }) {
  const location = useLocation()

  return (
    <div className="min-h-screen bg-surface pb-20">
      <div className="max-w-lg mx-auto px-4 pt-4">
        {!noHeader && title && (
          <h1 className="text-2xl font-bold text-gray-800 mb-1">{title}</h1>
        )}
        {!noHeader && subtitle && (
          <p className="text-gray-500 mb-6">{subtitle}</p>
        )}
        <div className={!title && !noHeader ? 'pt-4' : ''}>
          {children}
        </div>
      </div>

      <nav className="fixed bottom-0 left-0 right-0 z-50">
        <div className="max-w-lg mx-auto relative">
          <div className="bg-white/80 backdrop-blur-xl border-t border-gray-100 shadow-xl rounded-t-3xl flex justify-around items-center h-20 px-2">
            {tabs.map((t) => {
              const active = location.pathname === t.to
              const Icon = t.icon

              if (t.fab) {
                return (
                  <Link
                    key={t.to}
                    to={t.to}
                    className="flex flex-col items-center justify-center -mt-8"
                  >
                    <div className="w-14 h-14 rounded-full bg-gradient-to-br from-primary-600 to-secondary-600 shadow-lg shadow-primary-600/30 flex items-center justify-center text-white">
                      <Icon size={24} />
                    </div>
                    <span className={`text-[10px] mt-1.5 ${active ? 'text-primary-600 font-semibold' : 'text-gray-400'}`}>
                      {t.label}
                    </span>
                  </Link>
                )
              }

              return (
                <Link
                  key={t.to}
                  to={t.to}
                  className={`flex flex-col items-center justify-center gap-0.5 py-1 px-3 rounded-xl transition-colors ${
                    active ? 'text-primary-600' : 'text-gray-400'
                  }`}
                >
                  <Icon size={22} />
                  <span className={`text-[10px] leading-tight ${active ? 'font-semibold' : ''}`}>
                    {t.label}
                  </span>
                </Link>
              )
            })}
          </div>
        </div>
      </nav>
    </div>
  )
}

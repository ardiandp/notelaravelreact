import { Link, useLocation } from 'react-router-dom'
import { useAuth } from '../context/AuthContext'

const tabs = [
  { to: '/', label: 'Dashboard', icon: '🏠' },
  { to: '/attendance', label: 'Absensi', icon: '📍' },
  { to: '/leave-requests', label: 'Cuti', icon: '✈️' },
  { to: '/approvals', label: 'Setuju', icon: '✅' },
  { to: '/profile', label: 'Profil', icon: '👤' },
]

export default function Layout({ children, title, subtitle }) {
  const { user, logout } = useAuth()
  const location = useLocation()

  return (
    <div className="min-h-screen bg-gray-50">
      <div className="max-w-4xl mx-auto px-4 pt-4 pb-24">
        {title && <h1 className="text-2xl font-bold text-gray-800 mb-1">{title}</h1>}
        {subtitle && <p className="text-gray-500 mb-6">{subtitle}</p>}

        <div className={title ? '' : 'pt-4'}>
          {children}
        </div>
      </div>

      <nav className="fixed bottom-0 left-0 right-0 bg-white border-t shadow-lg z-50">
        <div className="max-w-4xl mx-auto flex justify-around items-center h-16">
          {tabs.map((t) => {
            const active = location.pathname === t.to
            return (
              <Link key={t.to} to={t.to} className={`flex flex-col items-center gap-0.5 py-1 px-3 rounded-lg transition-colors ${active ? 'text-indigo-600' : 'text-gray-400'}`}>
                <span className="text-xl">{t.icon}</span>
                <span className={`text-[10px] leading-tight ${active ? 'font-semibold' : ''}`}>{t.label}</span>
              </Link>
            )
          })}
        </div>
      </nav>
    </div>
  )
}

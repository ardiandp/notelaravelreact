import { Link, useLocation } from 'react-router-dom'
import { useAuth } from '../context/AuthContext'

export default function Navbar() {
  const { user, logout } = useAuth()
  const location = useLocation()

  const links = [
    { to: '/', label: 'Dashboard' },
    { to: '/attendance', label: 'Absensi' },
    { to: '/leave-requests', label: 'Cuti' },
    { to: '/approvals', label: 'Persetujuan' },
  ]

  return (
    <nav className="bg-white shadow-sm border-b">
      <div className="max-w-4xl mx-auto px-4 h-14 flex items-center justify-between">
        <Link to="/" className="font-bold text-lg text-indigo-600">HRSI</Link>
        {user && (
          <div className="flex items-center gap-4 text-sm">
            {links.map((l) => (
              <Link key={l.to} to={l.to} className={`${location.pathname === l.to ? 'text-indigo-600 font-semibold' : 'text-gray-500'} hover:text-indigo-600`}>{l.label}</Link>
            ))}
            <Link to="/profile" className="text-gray-500 hover:text-indigo-600">{user.name}</Link>
            <button onClick={logout} className="text-red-500 hover:underline">Logout</button>
          </div>
        )}
      </div>
    </nav>
  )
}

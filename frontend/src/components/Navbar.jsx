import { Link } from 'react-router-dom'
import { useAuth } from '../context/AuthContext'

export default function Navbar() {
  const { user, logout } = useAuth()

  return (
    <nav className="bg-white shadow-sm border-b">
      <div className="max-w-4xl mx-auto px-4 h-14 flex items-center justify-between">
        <Link to="/" className="font-bold text-lg text-indigo-600">NoteApp</Link>
        {user && (
          <div className="flex items-center gap-4 text-sm">
            <span className="text-gray-500">{user.name}</span>
            <Link to="/profile" className="text-indigo-600 hover:underline">Profile</Link>
            <button onClick={logout} className="text-red-500 hover:underline">Logout</button>
          </div>
        )}
      </div>
    </nav>
  )
}

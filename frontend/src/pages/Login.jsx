import { useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import { useAuth } from '../context/AuthContext'
import { Mail, Lock, Eye, EyeOff } from 'lucide-react'

export default function Login() {
  const { login } = useAuth()
  const navigate = useNavigate()
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [showPw, setShowPw] = useState(false)
  const [error, setError] = useState('')
  const [loading, setLoading] = useState(false)

  const handleSubmit = async (e) => {
    e.preventDefault()
    setError('')
    setLoading(true)
    try {
      await login(email, password)
      navigate('/', { replace: true })
    } catch (err) {
      setError(err.response?.data?.message || 'Email atau password salah')
    } finally {
      setLoading(false)
    }
  }

  return (
    <div className="min-h-screen bg-surface flex flex-col items-center justify-center px-5 py-8">
      <div className="w-full max-w-sm mx-auto text-center mb-8">
        <div className="inline-flex items-center justify-center w-20 h-20 rounded-[20px] bg-gradient-to-br from-primary-600 to-secondary-600 shadow-xl shadow-primary-600/20 mb-5">
          <span className="text-white text-3xl font-bold">H</span>
        </div>
        <h1 className="text-2xl font-bold text-gray-800">HRIS Mobile</h1>
        <p className="text-sm text-gray-400 mt-1">Human Resource Information System</p>
      </div>

      <div className="w-full max-w-sm mb-8">
        <svg viewBox="0 0 320 160" className="w-full" fill="none">
          <circle cx="80" cy="80" r="60" fill="url(#g1)" opacity="0.08" />
          <circle cx="240" cy="50" r="40" fill="url(#g2)" opacity="0.06" />
          <rect x="60" y="70" width="60" height="40" rx="8" fill="#2563EB" opacity="0.12" />
          <rect x="140" y="55" width="60" height="40" rx="8" fill="#4F46E5" opacity="0.10" />
          <rect x="100" y="90" width="60" height="40" rx="8" fill="#6366F1" opacity="0.08" />
          <circle cx="145" cy="65" r="4" fill="#2563EB" opacity="0.25" />
          <circle cx="130" cy="80" r="3" fill="#4F46E5" opacity="0.20" />
          <circle cx="160" cy="75" r="3.5" fill="#6366F1" opacity="0.15" />
          <defs>
            <linearGradient id="g1" x1="0" y1="0" x2="1" y2="1">
              <stop stopColor="#2563EB" />
              <stop offset="1" stopColor="#4F46E5" />
            </linearGradient>
            <linearGradient id="g2" x1="0" y1="0" x2="1" y2="1">
              <stop stopColor="#6366F1" />
              <stop offset="1" stopColor="#2563EB" />
            </linearGradient>
          </defs>
        </svg>
      </div>

      <div className="w-full max-w-sm bg-white rounded-[24px] shadow-[0_4px_6px_-1px_rgba(0,0,0,0.05),0_10px_15px_-3px_rgba(0,0,0,0.08)] p-7">
        <h2 className="text-xl font-bold text-gray-800 mb-1">Selamat Datang</h2>
        <p className="text-sm text-gray-400 mb-6">Silakan masuk ke akun Anda</p>

        {error && (
          <div className="bg-red-50 border border-red-100 text-red-600 text-sm px-4 py-3 rounded-xl mb-5">
            {error}
          </div>
        )}

        <form onSubmit={handleSubmit} className="space-y-4">
          <div>
            <label className="text-xs font-medium text-gray-500 mb-1.5 block">Email</label>
            <div className="relative">
              <Mail size={18} className="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" />
              <input
                type="email" value={email} onChange={(e) => setEmail(e.target.value)}
                placeholder="nama@perusahaan.com" required
                className="w-full h-13 pl-11 pr-4 rounded-2xl border border-gray-200 bg-gray-50 text-sm text-gray-800 placeholder:text-gray-400 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition"
              />
            </div>
          </div>

          <div>
            <label className="text-xs font-medium text-gray-500 mb-1.5 block">Password</label>
            <div className="relative">
              <Lock size={18} className="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" />
              <input
                type={showPw ? 'text' : 'password'} value={password} onChange={(e) => setPassword(e.target.value)}
                placeholder="••••••••" required
                className="w-full h-13 pl-11 pr-11 rounded-2xl border border-gray-200 bg-gray-50 text-sm text-gray-800 placeholder:text-gray-400 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition"
              />
              <button type="button" onClick={() => setShowPw(!showPw)} className="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400">
                {showPw ? <EyeOff size={18} /> : <Eye size={18} />}
              </button>
            </div>
          </div>

          <button
            type="submit" disabled={loading}
            className="w-full h-13 rounded-2xl bg-gradient-to-r from-primary-600 to-secondary-600 text-white font-semibold shadow-lg shadow-primary-600/25 hover:scale-[1.02] hover:shadow-xl hover:shadow-primary-600/30 transition disabled:opacity-60"
          >
            {loading ? 'Memproses...' : 'Masuk'}
          </button>
        </form>
      </div>

      <div className="w-full max-w-sm mt-5 text-center">
        <Link to="/register" className="w-full inline-block h-13 leading-[52px] rounded-2xl border-2 border-gray-200 text-gray-600 font-semibold text-sm hover:border-primary-300 hover:text-primary-600 transition">
          Buat Akun Baru
        </Link>
        <p className="text-xs text-gray-400 mt-8">v1.0.0 &mdash; HRIS Mobile</p>
      </div>
    </div>
  )
}

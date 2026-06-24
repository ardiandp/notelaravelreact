import { useState } from 'react'
import { useAuth } from '../context/AuthContext'
import api from '../api/axios'
import { User, Lock, LogOut } from 'lucide-react'

export default function Profile() {
  const { user, logout } = useAuth()
  const [tab, setTab] = useState('profile')

  const [name, setName] = useState(user?.name || '')
  const [email, setEmail] = useState(user?.email || '')
  const [msg, setMsg] = useState('')
  const [msgType, setMsgType] = useState('')

  const [oldPw, setOldPw] = useState('')
  const [newPw, setNewPw] = useState('')
  const [pwMsg, setPwMsg] = useState('')
  const [pwMsgType, setPwMsgType] = useState('')

  const handleUpdate = async (e) => {
    e.preventDefault()
    setMsg('')
    try {
      await api.put('/profile', { name, email })
      setMsg('Profil berhasil diperbarui'); setMsgType('success')
      localStorage.setItem('user', JSON.stringify({ ...user, name, email }))
    } catch (err) {
      setMsg(err.response?.data?.message || 'Gagal memperbarui profil'); setMsgType('error')
    }
  }

  const handlePassword = async (e) => {
    e.preventDefault()
    setPwMsg('')
    if (!oldPw || !newPw) { setPwMsg('Lengkapi semua field'); setPwMsgType('error'); return }
    try {
      await api.put('/profile/password', { current_password: oldPw, new_password: newPw })
      setPwMsg('Password berhasil diubah'); setPwMsgType('success')
      setOldPw(''); setNewPw('')
    } catch (err) {
      setPwMsg(err.response?.data?.message || 'Gagal mengubah password'); setPwMsgType('error')
    }
  }

  return (
    <div>
      <div className="text-center mb-6">
        <div className="w-20 h-20 rounded-full bg-gradient-to-br from-primary-600 to-secondary-600 flex items-center justify-center text-white text-2xl font-bold mx-auto shadow-lg shadow-primary-600/20 mb-3">
          {user?.name?.charAt(0)?.toUpperCase() || 'U'}
        </div>
        <h2 className="text-lg font-bold text-gray-800">{user?.name}</h2>
        <p className="text-xs text-gray-400">{user?.email}</p>
      </div>

      <div className="flex gap-2 mb-5 p-1 bg-gray-100 rounded-2xl">
        <button onClick={() => setTab('profile')}
          className={`flex-1 flex items-center justify-center gap-1.5 h-10 rounded-xl text-sm font-medium transition ${tab === 'profile' ? 'bg-white text-primary-600 shadow-sm' : 'text-gray-500'}`}>
          <User size={16} /> Profil
        </button>
        <button onClick={() => setTab('password')}
          className={`flex-1 flex items-center justify-center gap-1.5 h-10 rounded-xl text-sm font-medium transition ${tab === 'password' ? 'bg-white text-primary-600 shadow-sm' : 'text-gray-500'}`}>
          <Lock size={16} /> Password
        </button>
      </div>

      {tab === 'profile' && (
        <div className="card p-5">
          {msg && (
            <div className={`text-sm px-4 py-3 rounded-xl mb-4 ${msgType === 'success' ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600'}`}>
              {msg}
            </div>
          )}
          <form onSubmit={handleUpdate} className="space-y-4">
            <div>
              <label className="text-xs font-medium text-gray-500 mb-1.5 block">Nama Lengkap</label>
              <input value={name} onChange={(e) => setName(e.target.value)}
                className="w-full h-12 px-4 rounded-2xl border border-gray-200 bg-gray-50 text-sm text-gray-800 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition" />
            </div>
            <div>
              <label className="text-xs font-medium text-gray-500 mb-1.5 block">Email</label>
              <input value={email} onChange={(e) => setEmail(e.target.value)}
                className="w-full h-12 px-4 rounded-2xl border border-gray-200 bg-gray-50 text-sm text-gray-800 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition" />
            </div>
            <button type="submit" className="btn-gradient w-full h-12 text-sm">Simpan Perubahan</button>
          </form>
        </div>
      )}

      {tab === 'password' && (
        <div className="card p-5">
          {pwMsg && (
            <div className={`text-sm px-4 py-3 rounded-xl mb-4 ${pwMsgType === 'success' ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600'}`}>
              {pwMsg}
            </div>
          )}
          <form onSubmit={handlePassword} className="space-y-4">
            <div>
              <label className="text-xs font-medium text-gray-500 mb-1.5 block">Password Saat Ini</label>
              <input type="password" value={oldPw} onChange={(e) => setOldPw(e.target.value)}
                className="w-full h-12 px-4 rounded-2xl border border-gray-200 bg-gray-50 text-sm text-gray-800 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition" />
            </div>
            <div>
              <label className="text-xs font-medium text-gray-500 mb-1.5 block">Password Baru</label>
              <input type="password" value={newPw} onChange={(e) => setNewPw(e.target.value)}
                className="w-full h-12 px-4 rounded-2xl border border-gray-200 bg-gray-50 text-sm text-gray-800 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition" />
            </div>
            <button type="submit" className="btn-gradient w-full h-12 text-sm">Ubah Password</button>
          </form>
        </div>
      )}

      <div className="mt-5 text-center">
        <button onClick={logout} className="inline-flex items-center gap-2 text-sm text-red-500 font-medium hover:text-red-600 transition">
          <LogOut size={16} /> Keluar
        </button>
      </div>
    </div>
  )
}

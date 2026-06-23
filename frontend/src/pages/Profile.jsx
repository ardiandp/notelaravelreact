import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import api from '../api/axios'
import { useAuth } from '../context/AuthContext'

export default function Profile() {
  const { user, logout } = useAuth()
  const navigate = useNavigate()
  const [name, setName] = useState(user?.name || '')
  const [email, setEmail] = useState(user?.email || '')
  const [currentPassword, setCurrentPassword] = useState('')
  const [newPassword, setNewPassword] = useState('')
  const [newPasswordConfirmation, setNewPasswordConfirmation] = useState('')
  const [msg, setMsg] = useState('')
  const [error, setError] = useState('')
  const [tab, setTab] = useState('profile')

  const updateProfile = async (e) => {
    e.preventDefault()
    try {
      const res = await api.put('/profile', { name, email })
      localStorage.setItem('user', JSON.stringify(res.data.user))
      setMsg('Profile updated')
    } catch (err) {
      setError(err.response?.data?.message || 'Update failed')
    }
  }

  const changePassword = async (e) => {
    e.preventDefault()
    if (newPassword !== newPasswordConfirmation) { setError('Passwords do not match'); return }
    try {
      await api.put('/profile/password', { current_password: currentPassword, password: newPassword, password_confirmation: newPasswordConfirmation })
      setCurrentPassword(''); setNewPassword(''); setNewPasswordConfirmation('')
      setMsg('Password changed')
    } catch (err) {
      setError(err.response?.data?.message || 'Password change failed')
    }
  }

  const handleLogout = () => { logout(); navigate('/login') }

  return (
    <div>
      <div className="flex gap-2 mb-6">
        <button onClick={() => setTab('profile')} className={`px-4 py-2 rounded-lg text-sm font-medium ${tab === 'profile' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-600 border'}`}>Profile</button>
        <button onClick={() => setTab('password')} className={`px-4 py-2 rounded-lg text-sm font-medium ${tab === 'password' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-600 border'}`}>Password</button>
      </div>
      {msg && <div className="bg-green-50 text-green-600 text-sm p-3 rounded-lg mb-4">{msg}</div>}
      {error && <div className="bg-red-50 text-red-600 text-sm p-3 rounded-lg mb-4">{error}</div>}

      {tab === 'profile' && (
        <form onSubmit={updateProfile} className="bg-white rounded-xl shadow-sm border p-6 space-y-4">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Name</label>
            <input type="text" value={name} onChange={(e) => setName(e.target.value)} className="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none" required />
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" value={email} onChange={(e) => setEmail(e.target.value)} className="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none" required />
          </div>
          <button type="submit" className="w-full bg-indigo-600 text-white py-2.5 rounded-lg font-medium hover:bg-indigo-700 transition">Save Changes</button>
        </form>
      )}

      {tab === 'password' && (
        <form onSubmit={changePassword} className="bg-white rounded-xl shadow-sm border p-6 space-y-4">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
            <input type="password" value={currentPassword} onChange={(e) => setCurrentPassword(e.target.value)} className="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none" required />
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">New Password</label>
            <input type="password" value={newPassword} onChange={(e) => setNewPassword(e.target.value)} className="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none" required minLength={8} />
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
            <input type="password" value={newPasswordConfirmation} onChange={(e) => setNewPasswordConfirmation(e.target.value)} className="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none" required />
          </div>
          <button type="submit" className="w-full bg-indigo-600 text-white py-2.5 rounded-lg font-medium hover:bg-indigo-700 transition">Change Password</button>
        </form>
      )}

      <div className="mt-6 text-center">
        <button onClick={handleLogout} className="text-sm text-red-500 hover:underline">Sign Out</button>
      </div>
    </div>
  )
}

import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom'
import { AuthProvider, useAuth } from './context/AuthContext'
import Login from './pages/Login'
import Register from './pages/Register'
import Dashboard from './pages/Dashboard'
import Profile from './pages/Profile'
import AttendancePage from './pages/AttendancePage'
import LeaveRequestsPage from './pages/LeaveRequestsPage'
import ApprovalsPage from './pages/ApprovalsPage'
import ProtectedRoute from './components/ProtectedRoute'

function HomeRoute() {
  const { user } = useAuth()
  return user ? <Dashboard /> : <Navigate to="/login" replace />
}

export default function App() {
  return (
    <BrowserRouter>
      <AuthProvider>
        <Routes>
          <Route path="/" element={<HomeRoute />} />
          <Route path="/login" element={<Login />} />
          <Route path="/register" element={<Register />} />
          <Route path="/attendance" element={<ProtectedRoute><AttendancePage /></ProtectedRoute>} />
          <Route path="/leave-requests" element={<ProtectedRoute><LeaveRequestsPage /></ProtectedRoute>} />
          <Route path="/approvals" element={<ProtectedRoute><ApprovalsPage /></ProtectedRoute>} />
          <Route path="/profile" element={<ProtectedRoute><Profile /></ProtectedRoute>} />
        </Routes>
      </AuthProvider>
    </BrowserRouter>
  )
}

import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom'
import { AuthProvider, useAuth } from './context/AuthContext'
import Login from './pages/Login'
import Register from './pages/Register'
import Dashboard from './pages/Dashboard'
import Profile from './pages/Profile'
import AttendancePage from './pages/AttendancePage'
import LeaveRequestsPage from './pages/LeaveRequestsPage'
import ApprovalsPage from './pages/ApprovalsPage'
import SchedulePage from './pages/SchedulePage'
import ProtectedRoute from './components/ProtectedRoute'
import Layout from './components/Layout'

function ProtectedLayout({ children, ...props }) {
  return <ProtectedRoute><Layout {...props}>{children}</Layout></ProtectedRoute>
}

function HomeRoute() {
  const { user } = useAuth()
  return user ? <Layout><Dashboard /></Layout> : <Navigate to="/login" replace />
}

export default function App() {
  return (
    <BrowserRouter>
      <AuthProvider>
        <Routes>
          <Route path="/" element={<HomeRoute />} />
          <Route path="/login" element={<Login />} />
          <Route path="/register" element={<Register />} />
          <Route path="/attendance" element={<ProtectedLayout noHeader><AttendancePage /></ProtectedLayout>} />
          <Route path="/leave-requests" element={<ProtectedLayout title="Pengajuan Cuti / Izin"><LeaveRequestsPage /></ProtectedLayout>} />
          <Route path="/approvals" element={<ProtectedLayout title="Persetujuan"><ApprovalsPage /></ProtectedLayout>} />
          <Route path="/schedule" element={<ProtectedLayout title="Kalender Kerja"><SchedulePage /></ProtectedLayout>} />
          <Route path="/profile" element={<ProtectedLayout><Profile /></ProtectedLayout>} />
        </Routes>
      </AuthProvider>
    </BrowserRouter>
  )
}

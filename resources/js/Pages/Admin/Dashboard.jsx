import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function AdminDashboard({ stats, recentUsers, recentNotes }) {
    const cards = [
        { label: 'Total Users', value: stats.totalUsers, color: 'bg-blue-500' },
        { label: 'Total Catatan', value: stats.totalNotes, color: 'bg-green-500' },
        { label: 'User Baru (Bulan Ini)', value: stats.newUsersThisMonth, color: 'bg-purple-500' },
        { label: 'Catatan Baru (Bulan Ini)', value: stats.newNotesThisMonth, color: 'bg-amber-500' },
    ];

    return (
        <AuthenticatedLayout header={<h2 className="text-xl font-semibold leading-tight text-gray-800">Admin Dashboard</h2>}>
            <Head title="Admin Dashboard" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        {cards.map((card, i) => (
                            <div key={i} className="overflow-hidden rounded-lg bg-white shadow">
                                <div className={`h-2 ${card.color}`} />
                                <div className="p-5">
                                    <p className="text-sm font-medium text-gray-500">{card.label}</p>
                                    <p className="mt-1 text-3xl font-bold text-gray-900">{card.value}</p>
                                </div>
                            </div>
                        ))}
                    </div>

                    <div className="mt-8 grid gap-6 lg:grid-cols-2">
                        <div className="rounded-lg bg-white p-6 shadow">
                            <h3 className="mb-4 text-lg font-semibold text-gray-800">User Terbaru</h3>
                            <div className="space-y-3">
                                {recentUsers.map((u) => (
                                    <div key={u.id} className="flex items-center justify-between border-b pb-2 text-sm">
                                        <div>
                                            <p className="font-medium text-gray-800">{u.name}</p>
                                            <p className="text-gray-500">{u.email}</p>
                                        </div>
                                        <span className="text-xs text-gray-400">{u.created_at}</span>
                                    </div>
                                ))}
                            </div>
                        </div>

                        <div className="rounded-lg bg-white p-6 shadow">
                            <h3 className="mb-4 text-lg font-semibold text-gray-800">Catatan Terbaru</h3>
                            <div className="space-y-3">
                                {recentNotes.map((n) => (
                                    <div key={n.id} className="border-b pb-2 text-sm">
                                        <p className="font-medium text-gray-800 truncate">{n.content}</p>
                                        <p className="text-xs text-gray-400">oleh {n.user} &middot; {n.created_at}</p>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, usePage } from '@inertiajs/react';
import { useState } from 'react';

export default function UsersIndex({ users }) {
    const { flash } = usePage().props;
    const [search, setSearch] = useState('');

    const filtered = users.data.filter(
        (u) => u.name.toLowerCase().includes(search.toLowerCase()) || u.email.toLowerCase().includes(search.toLowerCase()),
    );

    return (
        <AuthenticatedLayout header={<h2 className="text-xl font-semibold leading-tight text-gray-800">Manajemen User</h2>}>
            <Head title="Manajemen User" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    {flash.success && (
                        <div className="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-700 shadow">{flash.success}</div>
                    )}
                    {flash.error && (
                        <div className="mb-4 rounded-md bg-red-50 p-4 text-sm text-red-700 shadow">{flash.error}</div>
                    )}

                    <div className="rounded-lg bg-white p-6 shadow">
                        <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <input
                                type="text"
                                placeholder="Cari user..."
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                className="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:w-64 text-sm"
                            />
                            <Link
                                href={route('admin.users.create')}
                                className="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                            >
                                + Tambah User
                            </Link>
                        </div>

                        <div className="mt-6 overflow-x-auto">
                            <table className="w-full text-sm">
                                <thead>
                                    <tr className="border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <th className="pb-3 pr-4">Name</th>
                                        <th className="pb-3 pr-4">Email</th>
                                        <th className="pb-3 pr-4">Role</th>
                                        <th className="pb-3 pr-4">Tanggal</th>
                                        <th className="pb-3 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {filtered.map((u) => (
                                        <tr key={u.id} className="border-b last:border-0">
                                            <td className="py-3 pr-4 font-medium text-gray-800">{u.name}</td>
                                            <td className="py-3 pr-4 text-gray-600">{u.email}</td>
                                            <td className="py-3 pr-4">
                                                <span
                                                    className={`inline-flex rounded-full px-2 py-0.5 text-xs font-medium ${
                                                        u.is_admin ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-600'
                                                    }`}
                                                >
                                                    {u.is_admin ? 'Admin' : 'User'}
                                                </span>
                                            </td>
                                            <td className="py-3 pr-4 text-gray-500">{u.created_at}</td>
                                            <td className="py-3 text-right">
                                                <Link
                                                    href={route('admin.users.edit', u.id)}
                                                    className="text-indigo-600 hover:text-indigo-900 font-medium"
                                                >
                                                    Edit
                                                </Link>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>

                        {users.total > users.per_page && (
                            <div className="mt-6 flex flex-wrap items-center justify-between gap-2 text-sm text-gray-600">
                                <span>
                                    Showing {users.from}–{users.to} of {users.total}
                                </span>
                                <div className="flex gap-2">
                                    {users.links.map((link, i) => (
                                        <Link
                                            key={i}
                                            href={link.url || '#'}
                                            disabled={!link.url}
                                            className={`rounded px-3 py-1 text-sm ${
                                                link.active
                                                    ? 'bg-indigo-600 text-white'
                                                    : link.url
                                                      ? 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                                      : 'bg-gray-50 text-gray-400 cursor-not-allowed'
                                            }`}
                                            dangerouslySetInnerHTML={{ __html: link.label }}
                                        />
                                    ))}
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

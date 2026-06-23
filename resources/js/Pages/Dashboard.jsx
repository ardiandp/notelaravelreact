import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, usePage } from '@inertiajs/react';

export default function Dashboard() {
    const user = usePage().props.auth.user;

    return (
        <AuthenticatedLayout header={<h2 className="text-xl font-semibold leading-tight text-gray-800">Dashboard</h2>}>
            <Head title="Dashboard" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div className="mb-6 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 p-6 text-white shadow-lg sm:p-8">
                        <h3 className="text-lg font-semibold">Selamat datang, {user.name}!</h3>
                        <p className="mt-1 text-sm text-indigo-100">
                            {user.is_admin ? 'Anda login sebagai Admin' : 'Anda login sebagai User'}
                        </p>
                    </div>

                    <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <Link
                            href={route('notes.index')}
                            className="group rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200 transition hover:shadow-md"
                        >
                            <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600 text-xl">
                                &#9997;
                            </div>
                            <h4 className="mt-4 font-semibold text-gray-800 group-hover:text-indigo-600">Catatan</h4>
                            <p className="mt-1 text-sm text-gray-500">Kelola catatan Anda</p>
                        </Link>

                        <Link
                            href={route('profile.edit')}
                            className="group rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200 transition hover:shadow-md"
                        >
                            <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100 text-green-600 text-xl">
                                &#128100;
                            </div>
                            <h4 className="mt-4 font-semibold text-gray-800 group-hover:text-green-600">Profile</h4>
                            <p className="mt-1 text-sm text-gray-500">Lihat dan edit profil Anda</p>
                        </Link>

                        {user.is_admin && (
                            <Link
                                href={route('admin.dashboard')}
                                className="group rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200 transition hover:shadow-md"
                            >
                                <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-100 text-purple-600 text-xl">
                                    &#9881;
                                </div>
                                <h4 className="mt-4 font-semibold text-gray-800 group-hover:text-purple-600">Admin Panel</h4>
                                <p className="mt-1 text-sm text-gray-500">Kelola pengguna dan pengaturan</p>
                            </Link>
                        )}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

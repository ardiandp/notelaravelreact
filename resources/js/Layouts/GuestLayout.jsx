import { Link } from '@inertiajs/react';

export default function GuestLayout({ children }) {
    return (
        <div className="flex min-h-screen flex-col items-center justify-center bg-gradient-to-br from-indigo-50 via-white to-purple-50 px-4 py-12 sm:px-6 lg:px-8">
            <div className="w-full max-w-md">
                <div className="mb-8 text-center">
                    <Link href="/">
                        <div className="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-indigo-600 text-white text-xl font-bold shadow-lg">
                            N
                        </div>
                    </Link>
                    <h2 className="mt-4 text-2xl font-bold text-gray-900">Note App</h2>
                    <p className="mt-1 text-sm text-gray-500">Kelola catatan Anda dengan mudah</p>
                </div>
                <div className="rounded-xl bg-white px-6 py-8 shadow-lg ring-1 ring-gray-200 sm:px-8">
                    {children}
                </div>
            </div>
        </div>
    );
}

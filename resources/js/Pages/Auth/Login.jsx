import InputError from '@/Components/InputError';
import GuestLayout from '@/Layouts/GuestLayout';
import { Head, Link, useForm } from '@inertiajs/react';

export default function Login({ status, canResetPassword }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('login'), {
            onFinish: () => reset('password'),
        });
    };

    return (
        <GuestLayout>
            <Head title="Log in" />

            <h3 className="mb-6 text-lg font-semibold text-gray-900">Masuk ke akun Anda</h3>

            {status && (
                <div className="mb-4 rounded-md bg-green-50 p-3 text-sm font-medium text-green-700">{status}</div>
            )}

            <form onSubmit={submit} className="space-y-5">
                <div>
                    <label htmlFor="email" className="block text-sm font-medium text-gray-700">
                        Email
                    </label>
                    <input
                        id="email"
                        type="email"
                        value={data.email}
                        onChange={(e) => setData('email', e.target.value)}
                        className="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                        placeholder="nama@email.com"
                        autoComplete="username"
                        autoFocus
                    />
                    <InputError message={errors.email} className="mt-1" />
                </div>

                <div>
                    <label htmlFor="password" className="block text-sm font-medium text-gray-700">
                        Password
                    </label>
                    <input
                        id="password"
                        type="password"
                        value={data.password}
                        onChange={(e) => setData('password', e.target.value)}
                        className="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                        placeholder="Masukkan password"
                        autoComplete="current-password"
                    />
                    <InputError message={errors.password} className="mt-1" />
                </div>

                <div className="flex items-center justify-between">
                    <label className="flex items-center gap-2">
                        <input
                            type="checkbox"
                            checked={data.remember}
                            onChange={(e) => setData('remember', e.target.checked)}
                            className="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                        />
                        <span className="text-sm text-gray-600">Ingat saya</span>
                    </label>

                    {canResetPassword && (
                        <Link
                            href={route('password.request')}
                            className="text-sm text-indigo-600 hover:text-indigo-800 font-medium"
                        >
                            Lupa password?
                        </Link>
                    )}
                </div>

                <button
                    type="submit"
                    disabled={processing}
                    className="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50"
                >
                    {processing ? 'Memproses...' : 'Masuk'}
                </button>

                <p className="text-center text-sm text-gray-500">
                    Belum punya akun?{' '}
                    <Link href={route('register')} className="font-medium text-indigo-600 hover:text-indigo-800">
                        Daftar
                    </Link>
                </p>
            </form>
        </GuestLayout>
    );
}

import { useState, useEffect } from 'react';
import { Head } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import NoteList from '@/Components/NoteList';
import NoteForm from '@/Components/NoteForm';
import { getNotes } from '@/api/notes';

// Halaman utama untuk fitur catatan
export default function NotesIndex() {
    // State untuk menyimpan daftar catatan dan status loading
    const [notes, setNotes] = useState([]);
    const [loading, setLoading] = useState(true);

    // Mengambil data catatan dari API saat komponen dimuat
    useEffect(() => {
        getNotes()
            .then((res) => setNotes(res.data))
            .finally(() => setLoading(false));
    }, []);

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Catatan
                </h2>
            }
        >
            <Head title="Catatan" />

            <div className="py-12">
                <div className="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                    <div className="space-y-6">
                        {/* Form untuk menambah catatan baru */}
                        <NoteForm
                            onNoteCreated={(note) =>
                                setNotes((prev) => [note, ...prev])
                            }
                        />

                        {/* Daftar catatan dengan tombol hapus */}
                        <NoteList
                            notes={notes}
                            loading={loading}
                            onNoteDeleted={(id) =>
                                setNotes((prev) =>
                                    prev.filter((n) => n.id !== id),
                                )
                            }
                        />
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

import { deleteNote } from '@/api/notes';

// Komponen untuk menampilkan daftar catatan
export default function NoteList({ notes, loading, onNoteDeleted }) {
    // Tampilkan indikator loading saat data sedang diambil
    if (loading) {
        return (
            <div className="text-center text-gray-500 py-8">
                Memuat catatan...
            </div>
        );
    }

    // Tampilkan pesan jika belum ada catatan
    if (notes.length === 0) {
        return (
            <div className="text-center text-gray-500 py-8">
                Belum ada catatan. Tambahkan catatan baru di atas.
            </div>
        );
    }

    // Fungsi untuk menghapus catatan dengan konfirmasi
    const handleDelete = (id) => {
        if (window.confirm('Hapus catatan ini?')) {
            deleteNote(id).then(() => onNoteDeleted(id));
        }
    };

    return (
        <div className="space-y-3">
            {notes.map((note) => (
                <div
                    key={note.id}
                    className="flex items-start justify-between gap-4 rounded-lg bg-white p-4 shadow-sm ring-1 ring-gray-200"
                >
                    {/* Isi catatan */}
                    <p className="text-gray-800 whitespace-pre-wrap break-words flex-1">
                        {note.content}
                    </p>
                    {/* Tombol hapus catatan */}
                    <button
                        onClick={() => handleDelete(note.id)}
                        className="shrink-0 rounded-md bg-red-500 px-3 py-1 text-sm text-white transition hover:bg-red-600"
                    >
                        Delete
                    </button>
                </div>
            ))}
        </div>
    );
}

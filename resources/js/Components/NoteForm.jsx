import { useState } from 'react';
import { createNote } from '@/api/notes';

// Komponen form untuk menambah catatan baru
export default function NoteForm({ onNoteCreated }) {
    // State untuk isi catatan dan status pengiriman
    const [content, setContent] = useState('');
    const [submitting, setSubmitting] = useState(false);

    // Menangani submit form
    const handleSubmit = (e) => {
        e.preventDefault();
        if (!content.trim()) return;

        setSubmitting(true);
        createNote(content.trim())
            .then((res) => {
                onNoteCreated(res.data);
                setContent('');
            })
            .finally(() => setSubmitting(false));
    };

    return (
        <form
            onSubmit={handleSubmit}
            className="rounded-lg bg-white p-4 shadow-sm ring-1 ring-gray-200"
        >
            {/* Textarea untuk input catatan */}
            <textarea
                value={content}
                onChange={(e) => setContent(e.target.value)}
                placeholder="Tulis catatan baru..."
                rows="3"
                className="w-full resize-none rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
            />
            <div className="mt-3 flex justify-end">
                {/* Tombol submit, dinonaktifkan saat mengirim atau konten kosong */}
                <button
                    type="submit"
                    disabled={submitting || !content.trim()}
                    className="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700 disabled:opacity-50"
                >
                    {submitting ? 'Menyimpan...' : 'Tambah Catatan'}
                </button>
            </div>
        </form>
    );
}

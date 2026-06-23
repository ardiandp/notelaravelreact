import { useState, useEffect, useCallback } from 'react'
import api from '../api/axios'
import Navbar from '../components/Navbar'

export default function Dashboard() {
  const [notes, setNotes] = useState([])
  const [content, setContent] = useState('')
  const [error, setError] = useState('')

  const fetchNotes = useCallback(async () => {
    try {
      const res = await api.get('/notes')
      setNotes(res.data)
    } catch { setError('Failed to load notes') }
  }, [])

  useEffect(() => { fetchNotes() }, [fetchNotes])

  const addNote = async (e) => {
    e.preventDefault()
    if (!content.trim()) return
    try {
      const res = await api.post('/notes', { content: content.trim() })
      setNotes((prev) => [res.data, ...prev])
      setContent('')
    } catch { setError('Failed to add note') }
  }

  const deleteNote = async (id) => {
    try {
      await api.delete(`/notes/${id}`)
      setNotes((prev) => prev.filter((n) => n.id !== id))
    } catch { setError('Failed to delete note') }
  }

  return (
    <div className="min-h-screen bg-gray-50">
      <Navbar />
      <div className="max-w-2xl mx-auto px-4 py-8">
        <h1 className="text-2xl font-bold text-gray-800 mb-6">My Notes</h1>
        {error && <div className="bg-red-50 text-red-600 text-sm p-3 rounded-lg mb-4">{error}</div>}
        <form onSubmit={addNote} className="mb-8">
          <div className="flex gap-2">
            <input value={content} onChange={(e) => setContent(e.target.value)} className="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none" placeholder="Write a note..." required />
            <button type="submit" className="px-6 py-2.5 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition">Add</button>
          </div>
        </form>
        <div className="space-y-3">
          {notes.length === 0 && <p className="text-center text-gray-400 py-8">No notes yet. Write your first note above!</p>}
          {notes.map((note) => (
            <div key={note.id} className="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex justify-between items-start gap-4">
              <p className="text-gray-700 whitespace-pre-wrap flex-1">{note.content}</p>
              <button onClick={() => deleteNote(note.id)} className="text-red-400 hover:text-red-600 transition flex-shrink-0">
                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
              </button>
            </div>
          ))}
        </div>
      </div>
    </div>
  )
}

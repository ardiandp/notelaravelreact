import axios from 'axios';

// Base URL untuk API Laravel
const API_BASE = '/api';

// Mengambil semua catatan dari server
export const getNotes = () => axios.get(`${API_BASE}/notes`);

// Menambahkan catatan baru ke server
export const createNote = (content) => axios.post(`${API_BASE}/notes`, { content });

// Menghapus catatan berdasarkan ID
export const deleteNote = (id) => axios.delete(`${API_BASE}/notes/${id}`);

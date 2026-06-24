import { useState, useEffect } from 'react'
import api from '../api/axios'

const DAYS = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu']
const SHIFT_COLORS = {
  Pagi: 'bg-blue-100 text-blue-700',
  Siang: 'bg-orange-100 text-orange-700',
  Malam: 'bg-purple-100 text-purple-700',
}

function getShiftColor(nama) {
  return SHIFT_COLORS[nama] || 'bg-indigo-100 text-indigo-700'
}

function getDaysInMonth(year, month) {
  return new Date(year, month, 0).getDate()
}

function getFirstDayIndex(year, month) {
  const day = new Date(year, month - 1, 1).getDay()
  return day === 0 ? 6 : day - 1
}

function buildCalendar(year, month) {
  const totalDays = getDaysInMonth(year, month)
  const firstDay = getFirstDayIndex(year, month)
  const weeks = []
  let day = 1
  for (let w = 0; w < 6; w++) {
    const week = []
    for (let d = 0; d < 7; d++) {
      if ((w === 0 && d < firstDay) || day > totalDays) {
        week.push(null)
      } else {
        week.push(day++)
      }
    }
    weeks.push(week)
    if (day > totalDays) break
  }
  return weeks
}

export default function SchedulePage() {
  const now = new Date()
  const [bulan, setBulan] = useState(now.getMonth() + 1)
  const [tahun, setTahun] = useState(now.getFullYear())
  const [data, setData] = useState(null)
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState(null)

  useEffect(() => {
    setLoading(true)
    setError(null)
    api.get('/schedules', { params: { bulan, tahun } })
      .then((res) => setData(res.data))
      .catch((err) => setError(err.message || 'Gagal memuat kalender'))
      .finally(() => setLoading(false))
  }, [bulan, tahun])

  const today = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')}`

  const schedByDate = {}
  if (data?.schedules) {
    data.schedules.forEach((s) => { schedByDate[s.tanggal] = s })
  }

  const holidayByDate = {}
  if (data?.holidays) {
    data.holidays.forEach((h) => { holidayByDate[h.tanggal] = h })
  }

  const weeks = buildCalendar(tahun, bulan)

  const prevMonth = () => {
    if (bulan === 1) { setBulan(12); setTahun(tahun - 1) }
    else { setBulan(bulan - 1) }
  }

  const nextMonth = () => {
    if (bulan === 12) { setBulan(1); setTahun(tahun + 1) }
    else { setBulan(bulan + 1) }
  }

  const goToday = () => {
    setBulan(now.getMonth() + 1)
    setTahun(now.getFullYear())
  }

  const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']

  if (loading) {
    return (
      <div className="flex justify-center items-center py-20">
        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
      </div>
    )
  }

  if (error) {
    return (
      <div className="text-center py-20">
        <p className="text-red-500 mb-4">{error}</p>
        <button onClick={() => window.location.reload()} className="text-primary-600 underline">Muat ulang</button>
      </div>
    )
  }

  return (
    <div>
      <div className="flex items-center justify-between mb-4">
        <button onClick={prevMonth} className="p-2 hover:bg-gray-100 rounded-xl text-gray-600">&larr;</button>
        <div className="flex items-center gap-3">
          <h2 className="text-lg font-semibold text-gray-800">{monthNames[bulan - 1]} {tahun}</h2>
          {(bulan !== now.getMonth() + 1 || tahun !== now.getFullYear()) && (
            <button onClick={goToday} className="text-xs text-primary-600 border border-primary-300 rounded-full px-3 py-1 hover:bg-primary-50">Hari ini</button>
          )}
        </div>
        <button onClick={nextMonth} className="p-2 hover:bg-gray-100 rounded-xl text-gray-600">&rarr;</button>
      </div>

      <div className="card overflow-hidden">
        <div className="grid grid-cols-7 border-b bg-gray-50">
          {DAYS.map((d, i) => (
            <div key={d} className={`text-center text-xs font-semibold py-2.5 text-gray-500 ${i >= 5 ? 'text-red-400' : ''}`}>{d}</div>
          ))}
        </div>

        {weeks.map((week, wi) => (
          <div key={wi} className="grid grid-cols-7 border-b last:border-b-0">
            {week.map((day, di) => {
              if (day === null) return <div key={di} className="min-h-[76px] bg-gray-50/50"></div>

              const dateStr = `${tahun}-${String(bulan).padStart(2, '0')}-${String(day).padStart(2, '0')}`
              const isToday = dateStr === today
              const sched = schedByDate[dateStr]
              const holiday = holidayByDate[dateStr]
              const isWeekend = di >= 5

              return (
                <div
                  key={di}
                  className={`min-h-[76px] p-1.5 border-r last:border-r-0 relative ${isToday ? 'ring-2 ring-primary-500 ring-inset bg-primary-50/30' : ''} ${isWeekend && !sched ? 'bg-gray-50' : ''}`}
                >
                  <span className={`text-xs font-medium ${isToday ? 'text-primary-600' : isWeekend && !sched ? 'text-gray-400' : 'text-gray-600'}`}>{day}</span>

                  {holiday && !sched && (
                    <p className="text-[10px] text-red-500 leading-tight mt-0.5">{holiday.keterangan}</p>
                  )}

                  {sched && sched.shift && (
                    <div className="mt-1 space-y-0.5">
                      <span className={`inline-block text-[10px] font-medium px-1.5 py-0.5 rounded ${getShiftColor(sched.shift.nama)}`}>{sched.shift.nama}</span>
                      <span className={`inline-block text-[10px] px-1 py-0.5 rounded ${sched.work_from === 'wfo' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'}`}>
                        {sched.work_from === 'wfo' ? 'WFO' : 'WFA'}
                      </span>
                    </div>
                  )}

                  {!sched && holiday && (
                    <p className="text-[10px] text-red-500 leading-tight mt-0.5">{holiday.keterangan}</p>
                  )}
                </div>
              )
            })}
          </div>
        ))}
      </div>

      {data?.schedules?.length === 0 && data?.holidays?.length === 0 && (
        <div className="text-center py-10 text-gray-400">Belum ada jadwal dan libur untuk bulan ini</div>
      )}

      <div className="flex items-center gap-4 mt-4 text-xs text-gray-500">
        <span><span className="inline-block w-3 h-3 rounded bg-green-100 border border-green-200 align-middle mr-1"></span>WFO</span>
        <span><span className="inline-block w-3 h-3 rounded bg-yellow-100 border border-yellow-200 align-middle mr-1"></span>WFA</span>
        <span><span className="inline-block w-3 h-3 rounded bg-red-50 border border-red-200 align-middle mr-1"></span>Libur</span>
      </div>
    </div>
  )
}

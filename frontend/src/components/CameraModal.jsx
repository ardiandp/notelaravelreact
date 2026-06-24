import { useState, useRef, useEffect } from 'react'
import { Camera, Repeat, Check } from 'lucide-react'

export default function CameraModal({ onConfirm, onClose }) {
  const videoRef = useRef(null)
  const canvasRef = useRef(null)
  const [stream, setStream] = useState(null)
  const [captured, setCaptured] = useState(null)
  const [error, setError] = useState(null)

  useEffect(() => {
    startCamera()
    return () => stopCamera()
  }, [])

  const startCamera = async () => {
    try {
      const s = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment', width: { ideal: 640 }, height: { ideal: 480 } } })
      setStream(s)
      if (videoRef.current) videoRef.current.srcObject = s
    } catch {
      setError('Tidak dapat mengakses kamera')
    }
  }

  const stopCamera = () => {
    if (stream) stream.getTracks().forEach((t) => t.stop())
  }

  const capture = () => {
    const video = videoRef.current
    const canvas = canvasRef.current
    if (!video || !canvas) return
    canvas.width = video.videoWidth
    canvas.height = video.videoHeight
    canvas.getContext('2d').drawImage(video, 0, 0)
    setCaptured(canvas.toDataURL('image/jpeg', 0.8))
    stopCamera()
  }

  const retake = () => {
    setCaptured(null)
    startCamera()
  }

  return (
    <div className="fixed inset-0 bg-black/80 flex items-center justify-center z-50" onClick={onClose}>
      <div className="bg-white rounded-[24px] max-w-sm w-full mx-4 overflow-hidden shadow-2xl" onClick={(e) => e.stopPropagation()}>
        <div className="p-4">
          {error ? (
            <div className="h-48 flex items-center justify-center text-red-500 text-sm bg-gray-50 rounded-2xl">{error}</div>
          ) : captured ? (
            <img src={captured} alt="captured" className="w-full rounded-2xl" />
          ) : (
            <div className="relative bg-black rounded-2xl overflow-hidden">
              <video ref={videoRef} autoPlay playsInline className="w-full h-56 object-cover" />
              <div className="absolute inset-0 border-2 border-white/30 rounded-2xl pointer-events-none" />
            </div>
          )}
          <canvas ref={canvasRef} className="hidden" />
        </div>

        <div className="flex justify-center gap-4 p-4 border-t border-gray-100">
          {captured ? (
            <>
              <button onClick={retake} className="flex items-center gap-1.5 border-2 border-gray-200 h-11 px-6 rounded-2xl text-sm font-medium text-gray-600 hover:border-primary-300 transition">
                <Repeat size={16} /> Ulangi
              </button>
              <button onClick={() => onConfirm(captured)} className="flex items-center gap-1.5 btn-gradient h-11 px-6 text-sm">
                <Check size={16} /> Gunakan
              </button>
            </>
          ) : (
            <>
              <button onClick={onClose} className="border-2 border-gray-200 h-11 px-6 rounded-2xl text-sm font-medium text-gray-600 hover:border-red-300 transition">Batal</button>
              <button onClick={capture} className="flex items-center gap-1.5 btn-gradient h-11 px-6 text-sm">
                <Camera size={16} /> Ambil Foto
              </button>
            </>
          )}
        </div>
      </div>
    </div>
  )
}

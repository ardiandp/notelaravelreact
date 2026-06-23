import { useState, useRef, useEffect } from 'react'

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
      const s = await navigator.mediaDevices.getUserMedia({
        video: { facingMode: 'environment', width: { ideal: 1280 }, height: { ideal: 720 } }
      })
      setStream(s)
      if (videoRef.current) videoRef.current.srcObject = s
    } catch {
      setError('Tidak dapat mengakses kamera')
    }
  }

  const stopCamera = () => {
    if (stream) stream.getTracks().forEach(t => t.stop())
  }

  const capture = () => {
    const video = videoRef.current
    const canvas = canvasRef.current
    if (!video || !canvas) return
    canvas.width = video.videoWidth
    canvas.height = video.videoHeight
    canvas.getContext('2d').drawImage(video, 0, 0)
    const dataUrl = canvas.toDataURL('image/jpeg', 0.7)
    setCaptured(dataUrl)
    stopCamera()
  }

  const retake = () => {
    setCaptured(null)
    setError(null)
    startCamera()
  }

  const confirm = () => {
    if (captured) onConfirm(captured)
  }

  return (
    <div className="fixed inset-0 bg-black/80 flex items-center justify-center z-50" onClick={onClose}>
      <div className="bg-white rounded-xl max-w-sm w-full mx-4 overflow-hidden" onClick={e => e.stopPropagation()}>
        {error ? (
          <div className="p-8 text-center">
            <p className="text-red-500 mb-4">{error}</p>
            <button onClick={onClose} className="border px-6 py-2 rounded-lg text-sm">Tutup</button>
          </div>
        ) : captured ? (
          <div className="p-4">
            <img src={captured} alt="foto" className="w-full rounded-lg" />
            <canvas ref={canvasRef} className="hidden" />
          </div>
        ) : (
          <div className="p-4">
            <video ref={videoRef} autoPlay playsInline muted className="w-full rounded-lg" />
            <canvas ref={canvasRef} className="hidden" />
          </div>
        )}
        <div className="flex justify-center gap-4 p-4 border-t">
          {captured ? (
            <>
              <button onClick={retake} className="border px-6 py-2 rounded-lg text-sm">Ulangi</button>
              <button onClick={confirm} className="bg-indigo-600 text-white px-6 py-2 rounded-lg text-sm">Gunakan</button>
            </>
          ) : (
            <>
              <button onClick={onClose} className="border px-4 py-2 rounded-lg text-sm">Batal</button>
              <button onClick={capture} className="bg-indigo-600 text-white px-6 py-2 rounded-lg text-sm">Ambil Foto</button>
            </>
          )}
        </div>
      </div>
    </div>
  )
}

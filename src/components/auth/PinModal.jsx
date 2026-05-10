import { useEffect, useRef, useState } from "react";
import { FaCheckCircle, FaEnvelope } from "react-icons/fa";
import AlertMessage from "../ui/AlertMessage";
import PinDigit from "./PinDigit";

const PIN_LENGTH = 6;

/**
 * PinModal – modal de verificación de PIN por correo.
 */
export default function PinModal({
  email,
  onVerify,
  onResend,
  onClose,
  loading,
  error,
  success,
}) {
  const [pin, setPin] = useState(Array(PIN_LENGTH).fill(""));
  const inputs = useRef([]);

  useEffect(() => {
    inputs.current[0]?.focus();
  }, []);

  const handleChange = (i, val) => {
    const digit = val.replace(/\D/, "").slice(-1);
    const next = [...pin];
    next[i] = digit;
    setPin(next);
    if (digit && i < PIN_LENGTH - 1) inputs.current[i + 1]?.focus();
  };

  const handleKeyDown = (i, e) => {
    if (e.key === "Backspace" && !pin[i] && i > 0) {
      inputs.current[i - 1]?.focus();
    }
  };

  const handlePaste = (e) => {
    e.preventDefault();
    const text = e.clipboardData.getData("text").replace(/\D/g, "").slice(0, PIN_LENGTH);
    const next = Array(PIN_LENGTH).fill("");
    text.split("").forEach((d, i) => { next[i] = d; });
    setPin(next);
    inputs.current[Math.min(text.length, PIN_LENGTH - 1)]?.focus();
  };

  const isComplete = pin.every((d) => d !== "");

  return (
    <div className="pin-overlay">
      <div className="pin-modal">
        <button type="button" className="pin-close" onClick={onClose}>
          ✕
        </button>

        <div className="pin-icon-wrap">
          <FaEnvelope className="pin-icon" />
        </div>

        <h2 className="pin-title">Verificá tu correo</h2>
        <p className="pin-desc">
          Enviamos un código de <strong>{PIN_LENGTH} dígitos</strong> a
          <br />
          <span className="pin-email">{email}</span>
        </p>

        <div className="pin-fields" onPaste={handlePaste}>
          {pin.map((digit, i) => (
            <PinDigit
              key={i}
              index={i}
              digit={digit}
              inputRef={(el) => (inputs.current[i] = el)}
              onChange={handleChange}
              onKeyDown={handleKeyDown}
            />
          ))}
        </div>

        <AlertMessage type="danger" message={error} />

        {success && (
          <p className="pin-success">
            <FaCheckCircle style={{ marginRight: 6, verticalAlign: "middle" }} />
            {success}
          </p>
        )}

        <button
          type="button"
          className="pin-btn"
          onClick={() => onVerify(pin.join(""))}
          disabled={!isComplete || loading}
        >
          {loading ? "VERIFICANDO..." : "VERIFICAR"}
        </button>

        <p className="pin-resend">
          ¿No recibiste el código?{" "}
          <button
            type="button"
            className="pin-resend-btn"
            onClick={onResend}
            disabled={loading}
          >
            Reenviar
          </button>
        </p>
      </div>
    </div>
  );
}

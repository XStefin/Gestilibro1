import { useEffect, useRef, useState } from "react";
import PinDigit from "./PinDigit";

const PIN_LENGTH = 6;

/**
 * PinInputGroup – fila de 6 inputs para ingresar un PIN.
 * Props:
 *   onComplete: (pin: string) => void  – se llama cuando los 6 dígitos están llenos
 */
export default function PinInputGroup({ onComplete }) {
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

    const complete = next.every((d) => d !== "");
    if (complete) onComplete?.(next.join(""));
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
    if (text.length === PIN_LENGTH) onComplete?.(text);
  };

  const isComplete = pin.every((d) => d !== "");

  return { pin, inputs, handleChange, handleKeyDown, handlePaste, isComplete };
}

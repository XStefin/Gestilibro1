import { useState } from "react";

/**
 * useForm – manejo genérico de estado de formularios.
 * @param {object} initialValues
 * @returns { form, setForm, handleChange, resetForm, clearMessages,
 *            error, setError, success, setSuccess }
 */
export function useForm(initialValues) {
  const [form, setForm] = useState(initialValues);
  const [error, setError] = useState("");
  const [success, setSuccess] = useState("");

  /**
   * Manejador universal: text, email, password, number, checkbox, date
   * PIN (name="pin"): filtra non-digits y limita a 4 chars automáticamente
   */
  const handleChange = (e) => {
    const { name, value, type, checked } = e.target;

    setForm((prev) => {
      if (name === "pin") {
        return { ...prev, [name]: value.replace(/\D/g, "").slice(0, 4) };
      }
      if (type === "checkbox") {
        return { ...prev, [name]: checked };
      }
      return { ...prev, [name]: value };
    });

    if (error) setError("");
    if (success) setSuccess("");
  };

  const resetForm = () => setForm(initialValues);

  const clearMessages = () => {
    setError("");
    setSuccess("");
  };

  return {
    form,
    setForm,
    handleChange,
    resetForm,
    clearMessages,
    error,
    setError,
    success,
    setSuccess,
  };
}

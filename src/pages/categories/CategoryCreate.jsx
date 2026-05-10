import { useState } from "react";
import PageLayout from "../../components/layout/PageLayout";
import FormCard from "../../components/ui/FormCard";
import AlertMessage from "../../components/ui/AlertMessage";
import InputField from "../../components/ui/InputField";
import TextAreaField from "../../components/ui/TextAreaField";
import FormActions from "../../components/ui/FormActions";
import { useForm } from "../../hooks/useForm";
import { apiRequest } from "../../services/api";
import "./CategoryCreate.css";

export default function CategoryCreate() {
  const { form, handleChange, resetForm, error, setError, success, setSuccess } = useForm({
    nombre: "",
    descripcion: "",
  });
  const [loading, setLoading] = useState(false);

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!form.nombre.trim()) {
      setError("El nombre de la categoría es obligatorio.");
      return;
    }

    try {
      setLoading(true);
      setError("");
      setSuccess("");
      await apiRequest("/categorias", {
        method: "POST",
        body: JSON.stringify({
          nombre: form.nombre.trim(),
          descripcion: form.descripcion.trim(),
        }),
      });
      setSuccess("Categoría guardada correctamente.");
      resetForm();
    } catch (err) {
      setError(err.message || "Error al guardar la categoría.");
    } finally {
      setLoading(false);
    }
  };

  return (
    <PageLayout>
      <FormCard icon={null} title="Añadir Categoría">
        <AlertMessage type="danger" message={error} onClose={() => setError("")} />
        <AlertMessage type="success" message={success} onClose={() => setSuccess("")} />

        <form onSubmit={handleSubmit}>
          <InputField
            label="Nombre"
            name="nombre"
            value={form.nombre}
            onChange={handleChange}
            required
          />
          <TextAreaField
            label="Descripción"
            name="descripcion"
            value={form.descripcion}
            onChange={handleChange}
            rows={4}
          />

          <FormActions
            cancelTo="/categories"
            submitLabel="Guardar"
            loadingLabel="Guardando..."
            loading={loading}
          />
        </form>
      </FormCard>
    </PageLayout>
  );
}

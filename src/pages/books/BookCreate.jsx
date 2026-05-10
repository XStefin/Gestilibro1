import { useNavigate } from "react-router-dom";
import { FaPlus } from "react-icons/fa6";
import PageLayout from "../../components/layout/PageLayout";
import FormCard from "../../components/ui/FormCard";
import AlertMessage from "../../components/ui/AlertMessage";
import InputField from "../../components/ui/InputField";
import SelectField from "../../components/ui/SelectField";
import FormActions from "../../components/ui/FormActions";
import { useForm } from "../../hooks/useForm";
import { useApiList } from "../../hooks/useApiList";
import { apiRequest } from "../../services/api";
import { useMemo, useState } from "react";
import "./BookCreate.css";

const maxDate = (() => {
  const now = new Date();
  return new Date(now.getTime() - now.getTimezoneOffset() * 60000)
    .toISOString()
    .split("T")[0];
})();

export default function BookCreate() {
  const navigate = useNavigate();
  const { form, handleChange, resetForm, error, setError, success, setSuccess } = useForm({
    titulo: "", autor: "", editorial: "", anio: "", id_categoria: "", cantidad: 1,
  });
  const [loading, setLoading] = useState(false);
  const { data: categorias, loading: loadingCategorias } = useApiList("/categorias");

  const categoriaOptions = useMemo(
    () => categorias.map((c) => ({ value: c.id_categoria, label: c.nombre })),
    [categorias]
  );

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!form.titulo.trim()) return setError("El título es obligatorio.");
    if (!form.autor.trim()) return setError("El autor es obligatorio.");
    if (!form.anio) return setError("Debe seleccionar una fecha.");
    if (form.anio > maxDate) return setError("No puede seleccionar un año superior al actual.");
    if (!form.id_categoria) return setError("Debe seleccionar una categoría.");
    if (Number(form.cantidad) < 1) return setError("La cantidad debe ser mayor o igual a 1.");

    try {
      setLoading(true);
      setError(""); setSuccess("");
      await apiRequest("/libros", {
        method: "POST",
        body: JSON.stringify({
          titulo: form.titulo.trim(),
          autor: form.autor.trim(),
          editorial: form.editorial.trim(),
          anio: Number(form.anio.split("-")[0]),
          id_categoria: Number(form.id_categoria),
          cantidad: Number(form.cantidad),
          disponibilidad: Number(form.cantidad) > 0 ? "disponible" : "no_disponible",
        }),
      });
      setSuccess("Libro guardado correctamente.");
      resetForm();
      setTimeout(() => navigate("/books"), 1000);
    } catch (err) {
      setError(err.message || "No fue posible guardar el libro.");
    } finally {
      setLoading(false);
    }
  };

  return (
    <PageLayout>
      <FormCard icon={<FaPlus />} title="Añadir Libro">
        <AlertMessage type="danger" message={error} onClose={() => setError("")} />
        <AlertMessage type="success" message={success} onClose={() => setSuccess("")} />

        <form onSubmit={handleSubmit}>
          <InputField label="Título" name="titulo" value={form.titulo} onChange={handleChange} required />
          <InputField label="Autor" name="autor" value={form.autor} onChange={handleChange} required />
          <InputField label="Editorial" name="editorial" value={form.editorial} onChange={handleChange} />

          <div className="form-grid">
            <InputField
              label="Fecha de publicación"
              name="anio"
              type="date"
              value={form.anio}
              onChange={handleChange}
              max={maxDate}
              required
            />
            <SelectField
              label="Categoría"
              name="id_categoria"
              value={form.id_categoria}
              onChange={handleChange}
              required
              disabled={loadingCategorias}
              placeholder={loadingCategorias ? "Cargando categorías..." : "Seleccione una categoría"}
              options={categoriaOptions}
            />
            <InputField
              label="Cantidad"
              name="cantidad"
              type="number"
              min="1"
              step="1"
              value={form.cantidad}
              onChange={handleChange}
              required
            />
          </div>

          <FormActions
            cancelTo="/books"
            submitLabel="Guardar"
            loadingLabel="Guardando..."
            loading={loading}
          />
        </form>
      </FormCard>
    </PageLayout>
  );
}

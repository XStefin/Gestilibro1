import { useEffect, useState } from "react";
import { useNavigate, useParams } from "react-router-dom";
import PageLayout from "../../components/layout/PageLayout";
import FormCard from "../../components/ui/FormCard";
import AlertMessage from "../../components/ui/AlertMessage";
import InputField from "../../components/ui/InputField";
import TextAreaField from "../../components/ui/TextAreaField";
import FormActions from "../../components/ui/FormActions";
import { useForm } from "../../hooks/useForm";
import { apiRequest } from "../../services/api";
import "./CategoryEdit.css";

export default function CategoryEdit() {
  const { id } = useParams();
  const navigate = useNavigate();
  const { form, setForm, handleChange, error, setError, success, setSuccess } = useForm({
    nombre: "",
    descripcion: "",
  });
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);

  useEffect(() => {
    (async () => {
      try {
        setLoading(true);
        setError("");
        const data = await apiRequest(`/categorias/${id}`);
        const cat = data?.data || data;
        if (!cat) {
          setError("No se encontró la categoría.");
          return;
        }
        setForm({
          nombre: cat.nombre || "",
          descripcion: cat.descripcion || "",
        });
      } catch {
        setError("Error al cargar la categoría.");
      } finally {
        setLoading(false);
      }
    })();
  }, [id]);

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!form.nombre.trim()) {
      setError("El nombre de la categoría es obligatorio.");
      return;
    }

    try {
      setSaving(true);
      setError("");
      setSuccess("");
      await apiRequest(`/categorias/${id}`, {
        method: "PUT",
        body: JSON.stringify({
          nombre: form.nombre.trim(),
          descripcion: form.descripcion.trim(),
        }),
      });
      setSuccess("Categoría actualizada correctamente.");
      setTimeout(() => navigate("/categories"), 1000);
    } catch (err) {
      setError(err.message || "Error al actualizar la categoría.");
    } finally {
      setSaving(false);
    }
  };

  return (
    <PageLayout>
      <FormCard icon={null} title="Editar Categoría">
        <AlertMessage type="danger" message={error} onClose={() => setError("")} />
        <AlertMessage type="success" message={success} onClose={() => setSuccess("")} />

        {loading ? (
          <AlertMessage type="info" message="Cargando categoría..." />
        ) : (
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
              submitLabel="Actualizar"
              loadingLabel="Actualizando..."
              loading={saving}
            />
          </form>
        )}
      </FormCard>
    </PageLayout>
  );
}

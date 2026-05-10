import { useEffect, useState } from "react";
import { apiRequest } from "../services/api";

/**
 * useApiItem – hook genérico para cargar un recurso individual.
 * @param {string} endpoint  – p.ej. "/libros/5"
 * @returns { item, loading, error, found }
 */
export function useApiItem(endpoint) {
  const [item, setItem] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [found, setFound] = useState(true);

  useEffect(() => {
    if (!endpoint) return;

    const load = async () => {
      try {
        setLoading(true);
        setError("");
        const result = await apiRequest(endpoint);
        setItem(result?.data ?? result);
        setFound(true);
      } catch (err) {
        setFound(false);
        setError(err.message || `No se encontró el recurso`);
      } finally {
        setLoading(false);
      }
    };

    load();
  }, [endpoint]);

  return { item, loading, error, found };
}

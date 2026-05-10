import { useEffect, useState } from "react";
import { apiRequest } from "../services/api";

/**
 * useApiList – hook genérico para cargar una lista desde la API.
 * @param {string} endpoint  – p.ej. "/libros"
 * @returns { data, loading, error, reload }
 */
export function useApiList(endpoint) {
  const [data, setData] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  const load = async () => {
    try {
      setLoading(true);
      setError("");
      const result = await apiRequest(endpoint);
      setData(Array.isArray(result) ? result : result?.data ?? []);
    } catch (err) {
      setError(err.message || `Error al cargar ${endpoint}`);
      setData([]);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    load();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [endpoint]);

  return { data, loading, error, reload: load };
}

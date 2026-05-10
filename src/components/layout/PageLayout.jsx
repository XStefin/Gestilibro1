import Sidebar from "./Sidebar";

/**
 * PageLayout – layout base de todas las páginas internas.
 * Renderiza el Sidebar y el <main> con los hijos.
 */
export default function PageLayout({ children }) {
  return (
    <div className="page-layout">
      <Sidebar />
      <main className="page-content">{children}</main>
    </div>
  );
}

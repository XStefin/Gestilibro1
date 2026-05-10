import { BrowserRouter, Routes, Route } from "react-router-dom";
import Login from "../pages/Login";
import Dashboard from "../pages/Dashboard";
import CategoriesList from "../pages/categories/CategoriesList";
import CategoryCreate from "../pages/categories/CategoryCreate";
import CategoryEdit from "../pages/categories/CategoryEdit";
import BooksList from "../pages/books/BooksList";
import BookCreate from "../pages/books/BookCreate";
import BookEdit from "../pages/books/BookEdit";
import UsersList from "../pages/users/UsersList";
import UserCreate from "../pages/users/UserCreate";
import UserEdit from "../pages/users/UserEdit";
import LoansList from "../pages/loans/LoansList";
import LoanCreate from "../pages/loans/LoanCreate";
import LoanEdit from "../pages/loans/LoanEdit";

export default function AppRouter() {
  return (
    <BrowserRouter>
      <Routes>
        <Route path="/" element={<Login />} />
        <Route path="/login" element={<Login />} />
        <Route path="/dashboard" element={<Dashboard />} />
        <Route path="/categories" element={<CategoriesList />} />
        <Route path="/categories/create" element={<CategoryCreate />} />
        <Route path="/categories/edit/:id" element={<CategoryEdit />} />
        <Route path="/books" element={<BooksList />} />
        <Route path="/books/create" element={<BookCreate />} />
        <Route path="/books/edit/:id" element={<BookEdit />} />
        <Route path="/users" element={<UsersList />} />
        <Route path="/users/create" element={<UserCreate />} />
        <Route path="/users/edit/:id" element={<UserEdit />} />
        <Route path="/loans" element={<LoansList />} />
        <Route path="/loans/create" element={<LoanCreate />} />
        <Route path="/loans/edit/:id" element={<LoanEdit />} />
      </Routes>
    </BrowserRouter>
  );
}

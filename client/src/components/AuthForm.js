import React, { useState } from "react";
import axios from "axios";
import { Form, Button, Card } from "react-bootstrap";
import "./AuthForm.css";

const AuthForm = ({ mode, onClose, setUser }) => {
  const isRegister = mode === "register";
  const [formData, setFormData] = useState({ name: "", email: "", password: "" });
  const [message, setMessage] = useState("");

  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    const url = isRegister
      ? "http://localhost/RaiseIt/api/register.php"
      : "http://localhost/RaiseIt/api/login.php";

    try {
      const response = await axios.post(url, formData, { headers: { "Content-Type": "application/json" } });
      setMessage(response.data.message);
      if (response.data.success) {
        setUser({ name: formData.name, email: formData.email });
        localStorage.setItem("authToken", response.data.token);
        onClose();
      }
    } catch (error) {
      setMessage("Error: " + (error.response?.data?.message || error.message));
    }
  };

  return (
    <div className="auth-overlay">
      <Card className="auth-card p-4 position-relative">
        <span className="close-btn" onClick={onClose}>&times;</span>

        <Card.Body>
          <h3 className="text-center mb-4">{isRegister ? "Register" : "Login"}</h3>
          {message && <p className="text-center text-danger">{message}</p>}

          <Form onSubmit={handleSubmit}>
            {isRegister && (
              <Form.Group className="mb-3">
                <Form.Label>Name</Form.Label>
                <Form.Control type="text" placeholder="Enter your name" name="name" value={formData.name} onChange={handleChange} required />
              </Form.Group>
            )}

            <Form.Group className="mb-3">
              <Form.Label>Email address</Form.Label>
              <Form.Control type="email" placeholder="Enter email" name="email" value={formData.email} onChange={handleChange} required />
            </Form.Group>

            <Form.Group className="mb-3">
              <Form.Label>Password</Form.Label>
              <Form.Control type="password" placeholder="Enter password" name="password" value={formData.password} onChange={handleChange} required />
            </Form.Group>

            <Button variant="primary" type="submit" className="w-100">{isRegister ? "Sign Up" : "Login"}</Button>
          </Form>

          <div className="text-center mt-3">
            <p>
              {isRegister ? "Already have an account?" : "Don't have an account?"}  
              <span className="toggle-link" onClick={onClose}>{isRegister ? " Login here" : " Register here"}</span>
            </p>
          </div>
        </Card.Body>
      </Card>
    </div>
  );
};

export default AuthForm;

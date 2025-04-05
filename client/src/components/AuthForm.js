import React, { useState } from "react";
import { Form, Button, Modal } from "react-bootstrap";
import { API_ENDPOINTS } from "./apiService.js";
import "./AuthForm.css";

const AuthForm = ({ mode, onClose }) => {
  const [isRegister, setIsRegister] = useState(mode === "register");
  const [formData, setFormData] = useState({
    name: "",
    email: "",
    password: "",
    confirmPassword: "",
  });
  const [message, setMessage] = useState("");
  const [isLoading, setIsLoading] = useState(false);

  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setMessage("");
    setIsLoading(true);

    if (isRegister && formData.password !== formData.confirmPassword) {
      setMessage("Passwords do not match!");
      setIsLoading(false);
      return;
    }

    const endpoint = isRegister ? API_ENDPOINTS.REGISTER : API_ENDPOINTS.LOGIN;

    try {
      const response = await fetch(endpoint, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(isRegister ? {
          name: formData.name,
          email: formData.email,
          password: formData.password,
        } : {
          email: formData.email,
          password: formData.password,
        }),
      });

      const data = await response.json();

      if (data.status === "success") {
        setMessage(isRegister ? "Registration successful!" : "Login successful!");
        setTimeout(() => {
          onClose(); // Close the modal after success
        }, 1500);
      } else {
        setMessage(data.message || "Something went wrong. Try again.");
      }
    } catch (error) {
      console.error("Auth error:", error);
      setMessage("Connection error. Please try again later.");
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div className="auth-form-container-right">
      <Modal show={true} onHide={onClose} centered>
        <Modal.Body className="p-4 auth-form-container">
          <Button variant="close" onClick={onClose} className="position-absolute top-0 end-0 m-3"></Button>

          <h3 className="text-center mb-3">{isRegister ? "Register" : "Login"}</h3>
          {message && <p className="text-center text-danger">{message}</p>}

          <Form onSubmit={handleSubmit}>
            {isRegister && (
              <Form.Group className="mb-3">
                <Form.Label>Full Name</Form.Label>
                <Form.Control
                  type="text"
                  name="name"
                  placeholder="Enter your name"
                  value={formData.name}
                  onChange={handleChange}
                  required
                />
              </Form.Group>
            )}

            <Form.Group className="mb-3">
              <Form.Label>Email</Form.Label>
              <Form.Control
                type="email"
                name="email"
                placeholder="Enter email"
                value={formData.email}
                onChange={handleChange}
                required
              />
            </Form.Group>

            <Form.Group className="mb-3">
              <Form.Label>Password</Form.Label>
              <Form.Control
                type="password"
                name="password"
                placeholder="Enter password"
                value={formData.password}
                onChange={handleChange}
                required
              />
            </Form.Group>

            {isRegister && (
              <Form.Group className="mb-3">
                <Form.Label>Re-enter Password</Form.Label>
                <Form.Control
                  type="password"
                  name="confirmPassword"
                  placeholder="Re-enter password"
                  value={formData.confirmPassword}
                  onChange={handleChange}
                  required
                />
              </Form.Group>
            )}

            <Button
              variant="primary"
              type="submit"
              className="w-100 mb-3"
              disabled={isLoading}
            >
              {isLoading ? (isRegister ? "Registering..." : "Logging in...") : (isRegister ? "Register" : "Login")}
            </Button>
          </Form>

          <p className="text-center mt-3">
            {isRegister ? "Already have an account?" : "Don't have an account?"}{" "}
            <span
              className="text-primary cursor-pointer"
              onClick={() => setIsRegister(!isRegister)}
            >
              {isRegister ? "Login" : "Register"}
            </span>
          </p>
        </Modal.Body>
      </Modal>
    </div>
  );
};

export default AuthForm;

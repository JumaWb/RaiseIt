import React, { useState } from "react";
import { Form, Button, Card } from "react-bootstrap";
import { API_ENDPOINTS } from "./apiService.js";
import "./AuthForm.css";

const AuthForm = ({ mode, onClose, setUser }) => {
  const isRegister = mode === "register";
  const [formData, setFormData] = useState({ 
    full_name: "", 
    email: "", 
    password: "",
    confirm_password: "" 
  });
  const [message, setMessage] = useState("");
  const [isLoading, setIsLoading] = useState(false);

  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setIsLoading(true);
    setMessage("");
    
    const dataToSend = isRegister ? formData : {
      email: formData.email,
      password: formData.password
    };
    
    const url = isRegister ? API_ENDPOINTS.REGISTER : API_ENDPOINTS.LOGIN;

    try {
      const response = await fetch(url, { 
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(dataToSend)
      });
      
      const data = await response.json();
      
      if (data.status === "success") {
        setMessage(data.message);
        setUser({ 
          name: isRegister ? formData.full_name : data.user_name || formData.email, 
          email: formData.email 
        });
        
        if (data.token) {
          localStorage.setItem("authToken", data.token);
        }
        
        setTimeout(() => onClose(), 1500); 
      } else {
        setMessage(data.message || "An error occurred");
      }
    } catch (error) {
      console.error("Authentication error:", error);
      setMessage("Connection error. Please try again later.");
    } finally {
      setIsLoading(false);
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
                <Form.Label>Full Name</Form.Label>
                <Form.Control 
                  type="text" 
                  placeholder="Enter your full name" 
                  name="full_name" 
                  value={formData.full_name} 
                  onChange={handleChange} 
                  required 
                />
              </Form.Group>
            )}

            <Form.Group className="mb-3">
              <Form.Label>Email address</Form.Label>
              <Form.Control 
                type="email" 
                placeholder="Enter email" 
                name="email" 
                value={formData.email} 
                onChange={handleChange} 
                required 
              />
            </Form.Group>

            <Form.Group className="mb-3">
              <Form.Label>Password</Form.Label>
              <Form.Control 
                type="password" 
                placeholder="Enter password" 
                name="password" 
                value={formData.password} 
                onChange={handleChange} 
                required 
              />
            </Form.Group>

            {isRegister && (
              <Form.Group className="mb-3">
                <Form.Label>Confirm Password</Form.Label>
                <Form.Control 
                  type="password" 
                  placeholder="Confirm your password" 
                  name="confirm_password" 
                  value={formData.confirm_password} 
                  onChange={handleChange} 
                  required 
                />
              </Form.Group>
            )}

            <Button 
              variant="primary" 
              type="submit" 
              className="w-100" 
              disabled={isLoading}
            >
              {isLoading ? "Processing..." : (isRegister ? "Sign Up" : "Login")}
            </Button>
          </Form>

          <div className="text-center mt-3">
            <p>
              {isRegister ? "Already have an account?" : "Don't have an account?"}  
              <Button 
                variant="link" 
                className="p-0 ms-1 toggle-link" 
                onClick={() => onClose()}
              >
                {isRegister ? "Login here" : "Register here"}
              </Button>
            </p>
          </div>
        </Card.Body>
      </Card>
    </div>
  );
};

export default AuthForm;
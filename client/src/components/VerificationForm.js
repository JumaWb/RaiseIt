import React, { useState } from "react";
import { Form, Button, Card } from "react-bootstrap";
import { useNavigate, useLocation } from "react-router-dom";
import { API_ENDPOINTS } from "./apiService.js";
import "./AuthForm.css";

const VerificationForm = () => {
  const navigate = useNavigate();
  const location = useLocation();
  const email = new URLSearchParams(location.search).get("email");

  const [otp, setOtp] = useState("");
  const [message, setMessage] = useState("");
  const [isLoading, setIsLoading] = useState(false);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setIsLoading(true);
    setMessage("");

    try {
      const response = await fetch(API_ENDPOINTS.VERIFY_EMAIL, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email, otp }),
      });

      const data = await response.json();

      if (data.status === "success") {
        setMessage("Verification successful! Redirecting...");
        setTimeout(() => {
          navigate("/login");
        }, 1500);
      } else {
        setMessage(data.message || "Invalid code. Try again.");
      }
    } catch (error) {
      console.error("Verification error:", error);
      setMessage("Connection error. Please try again later.");
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div className="auth-overlay">
      <Card className="auth-card p-4">
        <Card.Body>
          <h3 className="text-center mb-4">Email Verification</h3>
          <p className="text-center">Enter the 6-digit code sent to {email}</p>

          {message && <p className="text-center text-danger">{message}</p>}

          <Form onSubmit={handleSubmit}>
            <Form.Group className="mb-3">
              <Form.Label>Verification Code</Form.Label>
              <Form.Control
                type="text"
                placeholder="Enter OTP"
                value={otp}
                onChange={(e) => setOtp(e.target.value)}
                required
              />
            </Form.Group>

            <Button variant="success" type="submit" className="w-100" disabled={isLoading}>
              {isLoading ? "Verifying..." : "Verify"}
            </Button>
          </Form>
        </Card.Body>
      </Card>
    </div>
  );
};

export default VerificationForm;

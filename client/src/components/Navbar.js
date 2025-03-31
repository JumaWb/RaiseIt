import React, { useState, useEffect } from "react";
import { Navbar, Nav, Button } from "react-bootstrap";

const CustomNavbar = ({ onAuth }) => {
  const [isLoggedIn, setIsLoggedIn] = useState(false);

  useEffect(() => {
    const token = localStorage.getItem("authToken");
    setIsLoggedIn(!!token);
  }, []);

  const handleLogout = () => {
    localStorage.removeItem("authToken");
    setIsLoggedIn(false);
  };

  return (
    <Navbar expand="lg" className="custom-navbar px-4 py-2">
      <Navbar.Brand href="#" className="text-warning fw-bold fs-4 d-flex align-items-center">
        RaiseIt{" "}
        <span
          className="ms-2 d-flex align-items-center justify-content-center"
          style={{
            width: "35px",
            height: "35px",
            backgroundColor: "#FFD700", // Strong Yellow
            color: "#000", // Black text for contrast
            fontStyle: "italic",
            fontSize: "1.1em",
            fontFamily: "'Playfair Display', serif", // Unique italic font
            fontWeight: "bold",
            borderRadius: "50%", // Circular shape
            textAlign: "center",
            display: "inline-flex",
          }}
        >
          Ri
        </span>
      </Navbar.Brand>
      <Nav className="ms-auto d-flex align-items-center">
        {!isLoggedIn ? (
          <>
            <Button
              variant="outline-light"
              className="me-2 rounded-pill px-4 py-2 fw-bold shadow-sm"
              onClick={() => onAuth("login")}
            >
              Login
            </Button>
            <Button
              variant="primary"
              className="rounded-pill px-4 py-2 fw-bold shadow-sm"
              onClick={() => onAuth("register")}
            >
              Register
            </Button>
          </>
        ) : (
          <Button
            variant="danger"
            className="rounded-pill px-4 py-2 fw-bold shadow-sm"
            onClick={handleLogout}
          >
            Logout
          </Button>
        )}
      </Nav>
    </Navbar>
  );
};

export default CustomNavbar;

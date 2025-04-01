import React, { useState, useEffect } from "react";
import { Navbar, Nav, Button } from "react-bootstrap";

const HomeNavbar = ({ onAuth }) => {
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
            backgroundColor: "#FFD700",
            color: "#000",
            fontStyle: "italic",
            fontSize: "1.1em",
            fontFamily: "'Playfair Display', serif",
            fontWeight: "bold",
            borderRadius: "50%",
            textAlign: "center",
            display: "inline-flex",
          }}
        >
          Ri
        </span>
      </Navbar.Brand>
      <Nav className="ms-auto d-flex align-items-center">
        <Nav.Link href="/events" className="text-light fw-bold">Events</Nav.Link>
        <Nav.Link href="/aboutus" className="text-light fw-bold">About Us</Nav.Link>
        <Nav.Link href="/contact" className="text-light fw-bold">Contact</Nav.Link>
        {!isLoggedIn ? (
          <Button
            variant="outline-light"
            className="ms-3 rounded-pill px-4 py-2 fw-bold shadow-sm"
            onClick={() => onAuth("login")}
          >
            Login
          </Button>
        ) : (
          <Button
            variant="danger"
            className="ms-3 rounded-pill px-4 py-2 fw-bold shadow-sm"
            onClick={handleLogout}
          >
            Logout
          </Button>
        )}
      </Nav>
    </Navbar>
  );
};

export default HomeNavbar;
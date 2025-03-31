import React from "react";
import { Container } from "react-bootstrap";
import "./Footer.css"; // Optional for custom styling

const Footer = () => {
  return (
    <footer className="custom-footer">
      <Container className="text-center py-3">
        <p>&copy; {new Date().getFullYear()} RaiseIt. All rights reserved.</p>
      </Container>
    </footer>
  );
};

export default Footer;

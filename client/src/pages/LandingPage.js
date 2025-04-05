import React, { useState } from "react";
import { Container, Row, Col, Button } from "react-bootstrap";
import AuthForm from "../components/AuthForm";
import CustomNavbar from "../components/Navbar";
import Footer from "../components/Footer";
import "./LandingPage.css";

const LandingPage = () => {
  const [authMode, setAuthMode] = useState(null);

  return (
    <>
      <CustomNavbar onAuth={setAuthMode} />

      <div className="landing-page">
        <Container fluid className="vh-100 d-flex align-items-center">
          <Row className="w-100">
            <Col md={6} className="text-center d-flex flex-column justify-content-center text-light p-5">
              <h1 className="display-4">Join RaiseIt Today</h1>
              <p className="lead">Make an impact by contributing to fundraising events.</p>
              <Button variant="primary" size="lg">Learn More</Button>
            </Col>
          </Row>
        </Container>

        {authMode && (
          <div className="auth-form-container-right">
            <AuthForm mode={authMode} onClose={() => setAuthMode(null)} />
          </div>
        )}
      </div>

      <Footer />
    </>
  );
};

export default LandingPage;

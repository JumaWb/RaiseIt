import React from "react";
import { Container, Row, Col, Button } from "react-bootstrap";
import AuthForm from "../components/AuthForm";
import "./Home.css";

const Home = () => {
  return (
    <div className="landing-page">
      <div className="overlay"></div> {/* Dark Overlay for better contrast */}
      <Container fluid className="vh-100 d-flex align-items-center">
        <Row className="w-100">
          {/* Left Side - Text & CTA */}
          <Col md={6} className="text-center d-flex flex-column justify-content-center text-white p-5">
            <h1 className="display-4 fw-bold">
              Join <span className="text-warning">RaiseIt</span> Today
            </h1>
            <p className="lead">
              Make an impact by contributing to fundraising events.
            </p>
            <Button variant="warning" size="lg" className="mt-3">
              Learn More
            </Button>
          </Col>

          {/* Right Side - Auth Form */}
          <Col md={6} className="d-flex justify-content-center">
            <AuthForm />
          </Col>
        </Row>
      </Container>
    </div>
  );
};

export default Home;

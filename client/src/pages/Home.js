import React, { useState } from "react";
import { Container, Row, Col, Button } from "react-bootstrap";
import AuthForm from "../components/AuthForm";
import CustomNavbar from "../components/Navbar";
import Footer from "../components/Footer";
import "./Home.css";

const Home = () => {
  const [authMode, setAuthMode] = useState(null);
  //const [user, setUser] = useState(null);

  return (
    <> 
      {/* Pass setAuthMode to Navbar */}
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

        {/* Show AuthForm when authMode is set */}
        {authMode && <AuthForm mode={authMode} onClose={() => setAuthMode(null)}  />}
      </div>

      <Footer />
    </>
  );
};

export default Home;

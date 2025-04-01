import React from "react";
import { Container, Row, Col, Button } from "react-bootstrap";
import { 
  FaFacebook, FaTwitter, FaLinkedin, FaInstagram, 
  FaHeart, FaHandHoldingHeart, FaGlobeAfrica, 
  FaPhoneAlt, FaEnvelope, FaMapMarkerAlt
} from "react-icons/fa";
import "./Footer.css";

const Footer = () => {
  return (
    <footer className="impact-footer">
      {/* Main Footer Content */}
      <Container>
        <Row className="g-4">
          {/* About Section */}
          <Col lg={4} md={6}>
            <div className="footer-brand mb-4">
              <FaHeart className="text-danger me-2" size={28} />
              <h3 className="d-inline-block mb-0" style={{ color: "#4f46e5" }}>RaiseIt</h3>
            </div>
            <p className="footer-mission">
              Empowering communities through compassionate giving. We connect donors with verified social causes to create meaningful impact across Africa.
            </p>
            <div className="impact-stats d-flex justify-content-between mt-4">
              <div className="text-center">
                <FaHandHoldingHeart size={24} className="text-primary mb-2" />
                <h5 className="mb-0">1,250+</h5>
                <small>Projects Funded</small>
              </div>
              <div className="text-center">
                <FaGlobeAfrica size={24} className="text-success mb-2" />
                <h5 className="mb-0">18</h5>
                <small>Countries Reached</small>
              </div>
              <div className="text-center">
                <FaHeart size={24} className="text-danger mb-2" />
                <h5 className="mb-0">50K+</h5>
                <small>Lives Touched</small>
              </div>
            </div>
          </Col>

          {/* Quick Links */}
          <Col lg={2} md={6}>
            <h5 className="footer-heading">Quick Links</h5>
            <ul className="footer-links">
              <li><a href="/causes">Our Causes</a></li>
              <li><a href="/stories">Impact Stories</a></li>
              <li><a href="/partners">Partners</a></li>
              <li><a href="/volunteer">Volunteer</a></li>
              <li><a href="/events">Events</a></li>
            </ul>
          </Col>

          {/* Donation Links */}
          <Col lg={2} md={6}>
            <h5 className="footer-heading">Get Involved</h5>
            <ul className="footer-links">
              <li><a href="/donate">Donate Now</a></li>
              <li><a href="/fundraise">Start Fundraising</a></li>
              <li><a href="/corporate">Corporate Giving</a></li>
              <li><a href="/recurring">Monthly Giving</a></li>
              <li><a href="/sponsor">Sponsor a Child</a></li>
            </ul>
          </Col>

          {/* Contact & Newsletter */}
          <Col lg={4} md={6}>
            <h5 className="footer-heading">Stay Connected</h5>
            <div className="contact-info mb-4">
              <p><FaMapMarkerAlt className="me-2" /> Mama Ngina Street, Nairobi, Kenya</p>
              <p><FaPhoneAlt className="me-2" /> +254 700 123456</p>
              <p><FaEnvelope className="me-2" /> info@raiseit.org</p>
            </div>
            
            <div className="newsletter">
              <h6>Join our newsletter</h6>
              <div className="input-group mb-3">
                <input 
                  type="email" 
                  className="form-control" 
                  placeholder="Your email" 
                  aria-label="Your email"
                />
                <Button variant="primary">Subscribe</Button>
              </div>
            </div>

            <div className="social-links mt-3">
              <a href="#" aria-label="Facebook"><FaFacebook /></a>
              <a href="#" aria-label="Twitter"><FaTwitter /></a>
              <a href="#" aria-label="LinkedIn"><FaLinkedin /></a>
              <a href="#" aria-label="Instagram"><FaInstagram /></a>
            </div>
          </Col>
        </Row>
      </Container>

      {/* Footer Bottom */}
      <div className="footer-bottom">
        <Container>
          <Row className="align-items-center">
            <Col md={6} className="text-center text-md-start">
              <p className="mb-0">
                &copy; {new Date().getFullYear()} RaiseIt. All donations are tax deductible.
              </p>
            </Col>
            <Col md={6}>
              <ul className="footer-legal-links d-flex justify-content-center justify-content-md-end mb-0">
                <li><a href="/privacy">Privacy Policy</a></li>
                <li><a href="/terms">Terms</a></li>
                <li><a href="/financials">Financial Reports</a></li>
                <li><a href="/transparency">Our Transparency</a></li>
              </ul>
            </Col>
          </Row>
        </Container>
      </div>
    </footer>
  );
};

export default Footer;
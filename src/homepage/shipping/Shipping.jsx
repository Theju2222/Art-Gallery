import React from 'react'
import './shipping.css'
// import logo from '../asset/image 1.png'
import { Link, useNavigate } from 'react-router-dom'
import { useContext } from 'react'
import { AuthContext } from '../../AuthProvider'


const Shipping = () => {
    const navigate = useNavigate()
    const {All_Product_Page,Catagory} = useContext(AuthContext)
  return (
    <>
      <div class="about-us-top"  >
        <img class="shipping" src="\images\shipping.jpg" alt="" />
        <h1>Terms &amp; Conditions</h1>
      </div>
      <div class="about-us-bottom">
        <div className="about-us-bottom-container">
          <p>
            <h2><strong>FREE shipping & other good things</strong></h2>
            <p>If you want your stuff there faster, reach out to us with your order details.</p>
            <p>&nbsp;</p>
            <h2><strong>FAQs:</strong></h2>
            <h2><strong>When will I receive my artwork?</strong></h2>
            <p>As soon as we get your order, our team will reach out to you regarding the Artwork Package, Shipment & Installing.</p>
            <p>&nbsp;</p>
            <h2><strong>How will my artwork be packaged?</strong></h2>
            <p>All artwork is covered securely and placed in adjustable corrugated inserts that lock the piece in a secure position.</p>
            <p>&nbsp;</p>
            <h2><strong>How do I track my order?</strong></h2>
            <p>You will receive an Order Confirmation email after successfully placing an order. As soon as we ship your artwork, an associate from the Grandiose team will get in touch with you.</p>
            <p>&nbsp;</p>
            <h2><strong>How will I get my artwork installed?</strong></h2>
            <p>Do not worry, an agent from our team will be there at the time of delivery who will take care of the art installation.</p>
            <p>&nbsp;</p>


           
          </p>
          <div>
          <a class="link-a themeButton " href="#/contactus">contact us</a>
          </div>
        </div>
      </div>

    </>
  )
}

export default Shipping
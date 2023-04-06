import React from 'react'
import './refund.css'
// import logo from '../asset/image 1.png'
import { Link, useNavigate } from 'react-router-dom'
import { useContext } from 'react'
import { AuthContext } from '../../AuthProvider'


const Refund = () => {
    const navigate = useNavigate()
    const {All_Product_Page,Catagory} = useContext(AuthContext)
  return (
    <>
      <div class="about-us-top"  >
        <img class="refund" src="\images\policy1.jpg" alt="" />
        <h1>Terms &amp; Conditions</h1>
      </div>
      <div class="about-us-bottom">
        <div className="about-us-bottom-container">
          <p>
            <h2><strong>What if I just don't like the Product?</strong></h2>
            <p>It’s simple: If you don’t love it, return it. We are committed to quality products and your satisfaction is 100% guaranteed.</p>
            <p>Our agent will be there at the time of delivery to install the art. Just fill out a simple form with the reasons you want to return and we will take care of all the work for you.</p>
            <ul>
              <li>Once you have seen the artwork, you will have to fill out the form at delivery.</li>
              <li>Special Collection & Limited Edition are FINAL SALE and not eligible for returns.</li>
              <li>Customers are responsible for return shipping costs.</li>
            </ul>
          </p>
          <div>
          <a class="link-a themeButton " href="#/contactus">contact us</a>
          </div>
        </div>
      </div>

    </>
  )
}

export default Refund
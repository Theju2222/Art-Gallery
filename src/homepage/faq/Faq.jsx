import React from 'react'
import './faq.css'
//  import logo from '../asset/image 1.png'
import { Link, useNavigate } from 'react-router-dom'
import { useContext } from 'react'
import { AuthContext } from '../../AuthProvider'


const FAQ = () => {
    const navigate = useNavigate()
    const {All_Product_Page,Catagory} = useContext(AuthContext)
  return (
    <>
      <div class="about-us-top"  >
        <img class="faq" src="\images\faq.jpg" alt="" />
        <h1>Terms &amp; Conditions</h1>
      </div>
      <div class="about-us-bottom">
        <div class="about-us-bottom-container">
          <p>
            <h2><strong>FAQ</strong></h2>
            <h4><strong>Can I get a custom painting?</strong></h4>
            <ul>
              <li>Yes, we undertake custom paintings. For further information, please <a href="#/contactus">contact us</a> with your requirements.</li>
              <li>A quotation will be shared once we receive the original photograph.</li>
              <li>Based on these details work we will give you the timeline.</li>
            </ul>
            <h4><strong>Can I reserve the paintings?</strong></h4>
            <ul>
              <li>Yes, you can reserve the paintings. For reservations, please <a href="#/contactus">contact us</a>.</li>
              <li>Reserved paintings are only valid for 15 days.</li>
            
            </ul>
            <h4><strong>Do you sell frames?</strong></h4>
            <ul>
              <li>Yes,</li>
              <ul>
              <li>Synthetic</li>
              <li>Wooden Frames</li>
              <li>Modern Frames</li>
              <li>Antique Frames</li>
              <li>Customize Framing options</li>
              </ul>
              <li>For purchase-related queries, please <a href="#/contactus">contact us</a>.</li>

            </ul>
            <h4><strong>How to maintain a painting?</strong></h4>
            <ul>
              <li>Do not touch the painting</li>
              <li>Keep away from the kids</li>
              <li>Never use a wet cloth to wipe the dust</li>
              <li>Use a feather duster twice a week to clean the artwork</li>
              <li>Do not use a heavy vacuum cleaner</li>
              <li>Use a soft half wet cloth to clean the frames</li>
              <li>Keep away from direct sunlight</li>
            </ul>
            <h4><strong>Can I sell my paintings?</strong></h4>
            <ul>
              <li>Yes, you can sell your paintings.</li>
              <li>For further information, please <a href="#/contactus">contact us</a>.</li>
            
            </ul>
            <h4><strong>Can I buy Art material?</strong></h4>
            <ul>
              <li>No, we do not sell art materials.</li>
              
            </ul>
            <h4><strong>Can I get my painting varnish from you?</strong></h4>
            <ul>
              <li>Yes, we do use varnish for protection for your paintings.</li>
              <li>We have professional in-house Art experts who will guide you on what kind of varnish you must get done.</li>
              <li>For further queries, please <a href="#/contactus">contact us</a>
.</li>
            </ul>
            <h4><strong>Can I get an art consultant?</strong></h4>
            <ul>
              <li>Yes, we have Art consultant experts, who will visit your place and understand the concept based on your interior theme & will suggest the best paintings for you.</li>
              <li>Please check our services for more information</li>
           
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

export default FAQ
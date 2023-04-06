import React from 'react'
import './blogs.css'
// import logo from '../asset/image 1.png'
import { Link, useNavigate } from 'react-router-dom'
import { useContext } from 'react'
import { AuthContext } from '../../AuthProvider'


const Blogs = () => {
    const navigate = useNavigate()
    const {All_Product_Page,Catagory} = useContext(AuthContext)
  return (
    <>
      <div class="about-us-top"  >
        <img class="blogs" src="\images\blogs.jpg" alt="" />
        {/* <h1>Terms &amp; Conditions</h1> */}
      </div>
      <p class="about-us-bottom-container123">LATEST STORIES </p>
      <div class="container">
        <div className="blog-posts">
        <img class="blogs" src="\images\b1.png" alt="" />
        <img class="blogs" src="\images\b2.png" alt="" />
        <img class="blogs" src="\images\b3.png" alt="" />
        <img class="blogs" src="\images\b4.png" alt="" />
        <img class="blogs" src="\images\b5.png" alt="" />
        <img class="blogs" src="\images\b6.png" alt="" />
        <img class="blogs" src="\images\b7.png" alt="" />



        </div>
      </div>
    </>
  )
}

export default Blogs
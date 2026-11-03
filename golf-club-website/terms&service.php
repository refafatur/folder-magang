<head>
    <!-- ...existing code... -->
    
    <!-- Add AOS CSS -->
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    
    <!-- Add AOS JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
</head>

<style>
        .privacy-container {
            max-width: 1200px;
            margin: 120px auto 60px;
            padding: 40px;
            font-family: 'Poppins', Arial, sans-serif;
            line-height: 1.8;
            background: #ffffff;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }

        .privacy-container h1 {
            color: #1a1a1a;
            text-align: center;
            margin-bottom: 40px;
            font-size: 3rem;
            font-weight: 600;
            position: relative;
            padding-bottom: 15px;
        }

        .privacy-container h1::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 3px;
            background: goldenrod;
        }

        .privacy-container h2 {
            color: #2c3e50;
            margin-top: 30px;
            margin-bottom: 20px;
            font-size: 1.8rem;
            font-weight: 600;
            border-bottom: 2px solid goldenrod;
            padding-bottom: 10px;
        }

        .privacy-container h3 {
            color: #34495e;
            font-size: 1.3rem;
            margin: 25px 0 15px;
            font-weight: 500;
        }

        .privacy-container p {
            margin-bottom: 20px;
            color: #505050;
            font-size: 1.05rem;
            line-height: 1.8;
        }

        .privacy-container ul {
            margin: 20px 0;
            padding-left: 20px;
            color: #505050;
        }

        .privacy-container ul li {
            margin-bottom: 12px;
            position: relative;
            padding-left: 20px;
        }

        .privacy-container ul li::before {
            content: '•';
            color: goldenrod;
            position: absolute;
            left: 0;
            font-weight: bold;
        }

        .privacy-section {
            margin-bottom: 40px;
            background: #f8f9fa;
            padding: 35px;
            border-radius: 12px;
            border: 1px solid #eee;
            transition: all 0.3s ease;
        }

        .privacy-section:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.05);
        }

        .contact-info {
            background: #2c3e50;
            color: white;
            padding: 30px;
            border-radius: 12px;
            margin-top: 30px;
        }

        .contact-info p {
            color: rgba(255,255,255,0.9);
        }

        .contact-info a {
            color: goldenrod;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .contact-info a:hover {
            color: #daa520;
        }

        @media (max-width: 768px) {
            .privacy-container {
                margin: 100px auto 40px;
                padding: 20px;
            }
            
            .privacy-container h1 {
                font-size: 2.2rem;
            }
            
            .privacy-container h2 {
                font-size: 1.5rem;
            }
            
            .privacy-section {
                padding: 25px;
            }
        }
    </style>

    <?php include 'includes/header.php'; ?>

    <div class="privacy-container">
        <h1 data-aos="fade-down" data-aos-duration="1000">Terms & Service</h1>
        
        <div class="privacy-section" data-aos="fade-up" data-aos-duration="1000">
            <p>The following Terms of Service (these "Terms") describe the terms and conditions on which Klub Golf Bogor Raya (sometimes referred to in these Terms as sometimes referred to in these Terms as "we" or "us") offers you access to this website (this "Site") and to related websites, applications, services and mobile applications in or to which these Terms are linked or referenced, as well as any service or mobile application linked to or accessible to or through the Site (collectively with this Site, the "Services").</p>

            <p>Before accessing and using the Services, please read these Terms carefully because they constitute a legal agreement between Klub Golf Bogor Raya and you.</p>

            <p>BY USING ANY OF THE SERVICES, YOU AGREE TO THESE TERMS AND AFFIRM THAT YOU ARE ABLE AND LEGALLY COMPETENT TO DO SO.</p>

            <p>IMPORTANT NOTE: These Terms contain provisions that limit our liability to you and require you to resolve any dispute with us through final and binding arbitration on an individual basis and not as part of any class or representative action. Please see "DISCLAIMER OF WARRANTIES & LIMITATIONS OF LIABILITY" (Section 8) and "DISPUTE RESOLUTION" (Section 10) below for more information.</p>

            <p>IF YOU DO NOT AGREE TO ANY OF THESE TERMS, THEN PLEASE DO NOT USE THE SERVICES.</p>
        </div>

        <div class="privacy-section" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="100">
            <h2>1. CHANGES TO TERMS</h2>
            <p>The Effective Date of these Terms is set forth at the top of this webpage. As we add new features, we may need to modify these Terms. Whenever possible, we will provide you with advance written notice of our changes. We will not make changes that have a retroactive effect unless we are legally required to do so. Your continued use of the Services after the Effective Date constitutes your acceptance of the amended Terms. The amended Terms supersede all previous versions of or agreements, notices or statements of or about the Terms.</p>
        </div>

        <div class="privacy-section" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">
            <h2>2. ADDITIONS TO TERMS</h2>
            <p>Certain Services and Promotions (as defined in Section 6 below) may be governed by additional terms and conditions presented in conjunction with them. You must agree to the additional terms before using such services. These Terms and any additional terms will apply equally. If any additional term is irrevocably inconsistent with any provision of these Terms, the additional term will prevail but solely to the extent of the inconsistency.</p>
        </div>

        <div class="privacy-section" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="300">
            <h2>3. PRIVACY</h2>
            <p>Please review our Privacy Policy, which is incorporated by reference, to learn about the information that we collect and how we process it.</p>
            
            <p>Without limiting the terms of our Privacy Policy, you understand that Klub Golf Bogor Raya does not and cannot guarantee that your use of the Services and/or the information provided by you through the Services will be private or secure. Klub Golf Bogor Raya is not responsible or liable to you for any lack of privacy or security you may experience. You are responsible for using the precautions and security measures best suited for your situation and intended use of the Site and Services. Klub Golf Bogor Raya reserves the right at all times to disclose any information as we deem necessary to satisfy any applicable law, regulation, legal process or governmental request.</p>
        </div>

        <div class="privacy-section" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="400">
            <h2>4. KLUB GOLF BOGOR RAYA CONTENT & TRADEMARK ACKNOWLEDGEMENTS</h2>
            <p>The Services, including any information, graphic, artwork, text, video clip, audio clip, trademark, logo, e-card template and other content provided on, in or through the Services, (collectively, "PBC Content") are owned by Klub Golf Bogor Raya or its licensors and protected under both Bogor, Indonesian and foreign copyright, trademark and other laws.</p>

            <p>Klub Golf Bogor Raya Course, Klub Golf Bogor Raya Venue Wedding, Klub Golf Bogor Raya Locker Room, Klub Golf Bogor Raya Vip Room, Klub Golf Bogor Raya Tondano Terrace, Klub Golf Bogor Raya Spa, Klub Golf Bogor Raya Santani Cafe, Klub Golf Bogor Raya Spa Treatment their respective images, logo designs, golf course, and individual golf hole designs are trademarks, service marks, and trade dress of Klub Golf Bogor Raya, all rights reserved, © 2019 Klub Golf Bogor Raya. Neither this website nor any element thereof, including without limitation, text, graphics, images, photographs, likenesses, or other materials, may be copied, reproduced, uploaded, posted, transmitted, or redistributed without the prior express written consent of Klub Golf Bogor Raya. Trademarks, service marks, and logo designs owned by third parties are the property of their respective owners and are used by permission.</p>

            <p>Klub Golf Bogor Raya grants to you a personal, nonexclusive, nontransferable and limited license (without the right to sublicense) to access and use the Services solely for your own personal use and subject to these Terms. You acknowledge that the trade secrets and intellectual property embodied in the Services have not been and will not be licensed or otherwise disclosed to you. All rights not expressly granted herein are reserved by Klub Golf Bogor Raya.</p>
        </div>

        <div class="privacy-section" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="500">
            <h2>5. USING THE SITE AND/OR SERVICES</h2>
            
            <h3>5.1. Eligibility</h3>
            <p>You must be the age of legal majority or older in your place of residence to use the Services.</p>

            <h3>5.2. Your Profile</h3>
            <p>You are required to provide certain information to use certain features of the Services. This information is collectively referred to as your "Profile." You agree that you will maintain your Profile information to ensure that it is current, complete and accurate. If you provide untrue, incomplete or inaccurate information, you understand that we have the right to terminate your Profile and use of the Services.</p>

            <p>You agree that Klub Golf Bogor Raya may contact you using the email address that you provide in your Profile.</p>

            <h3>5.3. Your Responsibilities</h3>
            <p>You may use the Services for lawful, non-commercial purposes only. You may not use the Services in any manner that could damage, disable, overburden, or impair our servers or networks or interfere with any other party's use and enjoyment of the Services.</p>

            <p>You may not attempt to gain unauthorized access to the Services, other users' Profiles or Klub Golf Bogor Raya computer systems or networks, through hacking, data mining or any other means. Without limiting any of the foregoing, you agree that you shall not (and you agree not to encourage or allow any third party to):</p>

            <ul>
            <li>copy, modify, adapt, translate, reverse engineer, decode or otherwise attempt to derive or gain access to any portion of the Services or PBC Content;</li>
            <li>remove any copyright, trademark or other proprietary rights notices contained in the Site and/or Services or PBC Content;</li>
            <li>use any robot, spider, site search/retrieval application, or other automated device, process or means to access, retrieve, scrape or index any portion of the Site and/or Services;</li>
            <li>rent, lease, lend, sell, sublicense, assign, distribute, publish, transfer or otherwise make available the Site and/or Services or any features or functionality of the Services, to any third party for any reason, including by making the Services available on a network where it is capable of being accessed by more than one device at any time;</li>
            <li>reformat or frame any portion of the web pages that are part of the Services;</li>
            <li>create more than one Profile under false or fraudulent pretenses;</li>
            <li>send and e-card or otherwise use the Services to collect information about any other user without his/her express prior written consent;</li>
            <li>use the Services in any way that violates any applicable federal, state, local or international law or regulation (including, without limitation, any laws regarding the export of data or software to and from the Indonesian or other countries).</li>
            </ul>

            <p>Klub Golf Bogor Raya has the discretion to terminate your access to the Services without notice for any violation of the above rules.</p>

            <p>You are solely responsible for any and all charges, fees and other costs related to use of the Services. If you access and use the Services on your smartphone, tablet or other mobile device, you must have wireless service through Wi-Fi or a participating mobile service provider. You agree that you are solely responsible for all message and data charges that you incur from your mobile service provider.</p>

            <p>Klub Golf Bogor Raya may from time to time offer Services that require text messaging (SMS) capability. We do not impose a separate charge for text messaging services but message and data rates may apply. All charges are billed by and payable to your mobile service provider. Please contact your mobile service provider for pricing plans and details. Klub Golf Bogor Raya is not liable for any delays in the receipt of any text messages as delivery is subject to effective transmission from your mobile service provider. Text messages are provided on an "as is" basis and may not be available in all areas at all times.</p>

            <p>Once you opt-in to receive text messages from us, the frequency of text messages that we send to you will depend on your transactions with us. By agreeing to receive text messages, you understand and agree that Klub Golf Bogor Raya may use an automatic telephone dialing system to deliver text messages to you and that your consent is not required as a condition of purchase for any goods or services.</p>

            <h3>5.4. Submissions</h3>
            <p>Klub Golf Bogor Raya may from time to time offer areas in the Services where you and other users can post or otherwise submit suggestions, ideas, notes, e-cards, concepts or other information or materials to or through the Services (collectively, "Submissions"). By sending, posting or transmitting Submissions to Klub Golf Bogor Raya (and/or our designees) or in or on any area of the Services, you grant Klub Golf Bogor Raya and our designees a worldwide, non-exclusive, sub-licensable (through multiple tiers), transferable, royalty-free, perpetual, irrevocable right to use, reproduce, sublicense (through multiple tiers), distribute, create derivative works of, perform and import your Submissions in any media now known or hereafter developed, for any purpose whatsoever, commercial or otherwise, without compensation to you. In other words, Klub Golf Bogor Raya has the automatic right to use your Submissions — including reproducing, disclosing, publishing or broadcasting your Submission — in any medium and for any purpose. Under no circumstances are you entitled to payment if Klub Golf Bogor Raya uses one of your Submissions. All Submissions are deemed non-confidential and non-proprietary.</p>

            <p>You are the owner of and are responsible for your Submissions. By posting a Submission, you represent and warrant that:</p>

            <ul>
            <li>you own or otherwise control all of the rights to your Submission, including without limitation all copyrights and trademarks;</li>
            <li>your Submission is true and accurate;</li>
            <li>your Submission does not violate the rights of any other person or entity, such as rights of privacy and publicity; and</li>
            <li>your Submission complies with applicable laws, rules and regulations.</li>
            </ul>

            <p>You acknowledge and agree that Klub Golf Bogor Raya has the right (but not the obligation) to alter, remove or refuse to post or allow to be posted any Submission. Klub Golf Bogor Raya takes no responsibility and assumes no liability for any Submission posted by you or any third party. Klub Golf Bogor Raya strongly encourages you not to disclose any personal information in your Submissions because other people can see and use the personal information in your Submissions. Klub Golf Bogor Raya is not responsible for information that you choose to communicate via Submissions.</p>
        </div>

        <div class="privacy-section" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="600">
            <h2>6. PROMOTIONS</h2>
            <p>From time to time, we may offer you the opportunity to participate in discounts, offers or other promotions (collectively, "Promotions"). Promotions are not open to employees (and their immediate family members or members of the same household) of Klub Golf Bogor Raya and its affiliates, agents and advertising and promotion agencies. You may not transfer, assign, sell, trade or barter any prize, premium of other benefits you receive through our Promotions.</p>

            <p>Unless required by law, any Promotion-related prize, premium or other benefit may not be combined with a benefit received in connection with any other Promotion. ANY PRIZE, PREMIUM OF OTHER BENEFIT IS AWARDED "AS IS" WITHOUT ANY EXPRESS OR IMPLIED WARRANTY OR GUARANTEE FROM Klub Golf Bogor Raya, INCLUDING BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NON-INFRINGEMENT.</p>

            <p>We reserve the right to modify, terminate or suspend the availability of Promotions and to correct errors or inconsistencies in Promotion-related materials. We may disqualify any individual who tampers with any Promotion or Promotion-related process. CAUTION: ANY ATTEMPT BY ANY INDIVIDUAL TO DELIBERATELY UNDERMINE THE LEGITIMATE OPERATION OF A PROMOTION IS A VIOLATION OF CRIMINAL AND CIVIL LAWS AND WE RESERVE THE RIGHT TO SEEK DAMAGES FROM SUCH INDIVIDUAL TO THE FULLEST EXTENT PERMITTED BY LAW.</p>

            <p>You agree to be bound by our decisions, which are final and binding in all matters relating to Promotions. Promotions are subject to all applicable federal, state and local laws, rules and regulations. Promotions are void anywhere that they are prohibited, restricted or taxed.</p>

            <p>BY PARTICIPATING IN A PROMOTION, YOU AGREE TO RELEASE THE Klub Golf Bogor Raya AND ITS AGENTS AND AFFILIATES FROM ANY AND ALL LIABILITY, LOSS OR DAMAGE ARISING FROM OR IN CONNECTION WITH PARTICIPATION IN ANY PROMOTION-RELATED ACTIVITY OR THE RECEIPT, USE AND/OR MISUSE OF ANY PRIZE OR PREMIUM THAT YOU MAY RECEIVE.</p>
        </div>

        <div class="privacy-section" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="700">
            <h2>7. THE KLUB GOLF BOGOR RAYA APP</h2>
            
            <h3>7.1. Actual Conditions; Assumption of Risk</h3>
            <p>When you use Klub Golf Bogor Raya App's map data, traffic, directions, and other Content, you may find that actual conditions differ from the map results and Content, so exercise your independent judgment and use the Klub Golf Bogor Raya App at your own risk. You're responsible at all times for your conduct and its consequences.</p>

            <h3>7.2. Prohibited Conduct</h3>
            <p>When using Klub Golf Bogor Raya App, you may not (or allow those acting on your behalf to):</p>
            <ul>
            <li>redistribute or sell any part of Klub Golf Bogor Raya App or create a new product or service based on Klub Golf Bogor Raya App;</li>
            <li>copy the Content (unless you are otherwise permitted to do so by applicable intellectual property law, including "fair use");</li>
            <li>mass download or create bulk feeds of the Content (or let anyone else do so);</li>
            <li>use Klub Golf Bogor Raya App to create or augment any other mapping-related dataset for use in a substitute service;</li>
            <li>use any part of Klub Golf Bogor Raya App with other people's products or services for real-time navigation or autonomous vehicle control;</li>
            <li>reverse engineer or attempt to extract the source code from Klub Golf Bogor Raya App;</li>
            <li>remove, obscure, or alter any terms of service, notices, or proprietary rights notices;</li>
            <li>do anything inappropriate, illegal, or in violation of others' rights.</li>
            </ul>

            <h3>7.3. Your Content in Klub Golf Bogor Raya App</h3>
            <p>Content you upload, submit, store, send, or receive through Klub Golf Bogor Raya App is subject to Klub Golf Bogor Raya's Universal Terms. However, content that remains exclusively local to your device is not uploaded or submitted to Klub Golf Bogor Raya, and is therefore not subject to that license.</p>
        </div>

        <div class="privacy-section" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="800">
            <h2>8. LINKS TO OTHER SERVICES</h2>
            <p>The Services may contain links to third-party websites and services, including social media (collectively, "Linked Services"). Linked Services are not under the control of Klub Golf Bogor Raya and Klub Golf Bogor Raya is not responsible for Linked Services or for any information or materials on, or any form of transmission received from, any Linked Services. The inclusion of a link does not imply endorsement by Klub Golf Bogor Raya of the Linked Services or any association with the operators of the Linked Services. Klub Golf Bogor Raya does not investigate, verify or monitor the Linked Services. Klub Golf Bogor Raya provides links to Linked Sites for your convenience only. You access Linked Services at your own risk.</p>
        </div>

        <div class="privacy-section" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="900">
            <h2>9. DISCLAIMER OF WARRANTIES & LIMITATIONS OF LIABILITY</h2>
            <p>Klub Golf Bogor Raya warrants that Klub Golf Bogor Raya has validly entered into these Terms and has the legal power to do so. You warrant that you have validly entered into these Terms and have the legal power to do so.</p>

            <p>EXCEPT AS EXPRESSLY PROVIDED ABOVE, THE SERVICES ARE PROVIDED "AS IS" AND "AS AVAILABLE" WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED. Klub Golf Bogor Raya specifically disclaims all warranties and conditions of any kind, including all implied warranties and conditions of merchantability, fitness for a particular purpose, title, non-infringement, freedom from defects, uninterrupted use and all warranties implied from any course of dealing or usage of trade. Klub Golf Bogor Raya does not warrant that (a) the Services will meet your requirements, (b) operation of the Services will be uninterrupted or virus- or error-free or (c) errors will be corrected. Any oral or written advice provided by Klub Golf Bogor Raya or its authorized agents does not and will not create any warranty. SOME JURISDICTIONS DO NOT ALLOW THE EXCLUSION OF IMPLIED WARRANTIES WHICH MEANS THAT SOME OR ALL OF THE ABOVE EXCLUSIONS MAY NOT APPLY TO YOU.</p>

            <p>YOU USE THE SERVICES AT YOUR OWN RISK. YOU AGREE THAT IN NO EVENT WILL Klub Golf Bogor Raya BE LIABLE (Y) FOR DAMAGES OF ANY KIND, INCLUDING DIRECT, INDIRECT, SPECIAL, EXEMPLARY, INCIDENTAL, CONSEQUENTIAL OR PUNITIVE DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES, LOSS OF USE, DATA OR PROFITS OR BUSINESS INTERRUPTION), HOWEVER CAUSED AND UNDER ANY THEORY OF LIABILITY, WHETHER ARISING IN ANY WAY IN CONNECTION WITH THESE TERMS AND WHETHER IN CONTRACT, STRICT LIABILITY OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) EVEN IF Klub Golf Bogor Raya HAS BEEN ADVISED OF THE POSSIBILITY OF SUCH DAMAGE OR (Z) FOR ANY OTHER CLAIM, DEMAND OR DAMAGES WHATSOEVER RESULTING FROM OR ARISING OUT OF OR IN CONNECTION WITH YOUR USE OF THE SERVICES. The foregoing disclaimer of liability will not apply to the extent prohibited by applicable law. IF FOR ANY REASON THE DISCLAIMERS OF WARRANTIES OR LIMITATIONS OF LIABILITY SET FORTH IN THIS SECTION 8 IS/ARE INAPPLICABLE OR UNENFORCEABLE FOR ANY REASON, THEN Klub Golf Bogor Raya MAXIMUM LIABILITY FOR ANY TYPE OF DAMAGES UNDER THESE TERMS IS LIMITED TO $1,000.</p>

            <p>You acknowledge and agree that the above limitations of liability together with the other provisions in these Terms that limit liability are essential terms and that Klub Golf Bogor Raya would not be willing to grant you the rights set forth in these Terms but for your agreement to the above limitations of liability.</p>

            <p>IF YOU ARE A Klub Golf Bogor Raya RESIDENT, YOU WAIVE YOUR RIGHTS WITH RESPECT TO Klub Golf Bogor Raya CIVIL CODE SECTION 17610, WHICH SAYS "A GENERAL RELEASE DOES NOT EXTEND TO CLAIMS WHICH THE CREDITOR DOES NOT KNOW OR SUSPECT TO EXIST IN HIS FAVOR AT THE TIME OF EXECUTING THE RELEASE, WHICH, IF KNOWN BY HIM MUST HAVE MATERIALLY AFFECTED HIS SETTLEMENT WITH THE DEBTOR".</p>
        </div>

        <div class="privacy-section" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="1000">
            <h2>10. INDEMNIFICATION</h2>
            <p>You agree to indemnify and defend Klub Golf Bogor Raya and its directors, officers, employees and agents from and against all losses, liabilities, actual or pending claims, actions, damages, expenses, costs of defense and reasonable attorneys' fees brought against Klub Golf Bogor Raya by any third party arising from your use of the Services or any violation of these Terms, the rights of a third party or applicable law. Klub Golf Bogor Raya reserves the right, at its own expense, to assume the exclusive defense and control of any matter subject to indemnification hereunder. No settlement that affects the rights or obligations of Klub Golf Bogor Raya may be made without Klub Golf Bogor Raya prior written approval.</p>
        </div>

        <div class="privacy-section" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="1100">
            <h2>11. DISPUTE RESOLUTION</h2>
            <p>These Terms are governed by and construed and enforced in accordance with the internal laws of the State of Klub Golf Bogor Raya without giving effect to the principles of conflicts of laws of such state and are binding upon the parties hereto in the Indonesian and worldwide. You and Klub Golf Bogor Raya agree that the United Nations Convention on Contracts for the International Sale of Goods does not apply to the interpretation or construction of these Terms.</p>

            <p>YOU AND Klub Golf Bogor Raya AGREE THAT EACH MAY BRING CLAIMS AGAINST THE OTHER ONLY ON AN INDIVIDUAL BASIS AND NOT AS A PLAINTIFF OR CLASS MEMBER IN ANY PURPORTED CLASS OR REPRESENTATIVE ACTION OR PROCEEDING.</p>

            <p>YOU AND Klub Golf Bogor Raya AGREE THAT ALL CLAIMS WILL BE RESOLVED BY BINDING ARBITRATION IN THE MANNER SPECIFIED IN THIS SECTION 10 AND THAT YOU AND Klub Golf Bogor Raya WAIVE ANY RIGHT TO BRING SUCH CLAIMS BEFORE ANY COURT OF LAW. RIGHTS THAT YOU WOULD HAVE IF YOU WENT TO COURT MAY BE UNAVAILABLE OR LIMITED IN ARBITRATION.</p>

            <p>Any dispute between you and Klub Golf Bogor Raya and its agents, employees, officers, directors, principals, successors, assigns, subsidiaries or affiliates arising from or relating to these Terms and their interpretation or the breach, termination or validity thereof, the relationships which result from these Terms, including disputes about the validity, scope or enforceability of these Terms to arbitration (collectively, "Covered Disputes") will be settled by binding arbitration in the State of Klub Golf Bogor Raya administered by the Indonesian Arbitration Association (IAA) in accordance with its rules (including its rules and procedures for consumer-related disputes) in effect on the date thereof ("IAA Rules"). Prior to initiating any arbitration, the initiating party will give the other party at least 60 days' advanced written notice of its intent to file for arbitration. Klub Golf Bogor Raya will provide such notice by email to the email address that you provided when you created your Profile and you must provide such notice to Klub Golf Bogor Raya by email at disputes@klubgolfbogorraya.com.</p>

            <p>Payment of all filing, administration and arbitrator fees will be governed by the IAA Rules. If, however, you are able to demonstrate that the costs of arbitration will be prohibitive for you as compared to the costs of litigation, Klub Golf Bogor Raya will pay as much of the filing, administration and arbitrator fees as the arbitrator deems necessary to prevent the arbitration from being cost-prohibitive for you. If the arbitrator determines the claim(s) you assert in the arbitration are frivolous, you agree to reimburse Klub Golf Bogor Raya for all fees associated with the arbitration that Klub Golf Bogor Raya paid on your behalf which you otherwise would be obligated to pay under the IAA Rules.</p>

            <p>A single arbitrator will be selected in accordance with the IAA Rules. The arbitration shall be conducted in the Indonesia language. The arbitrator will have the power to grant whatever relief would be available in court under law or in equity and any award of the arbitrator will be final and binding on each of the parties and may be entered as a judgment in any court of competent jurisdiction. The arbitrator will not, however, have the power to award punitive or exemplary damages, the right to which each party hereby waives. The arbitrator will apply applicable law and the provisions of these Terms and the failure to do so will be deemed an excess of arbitral authority and grounds for judicial review. The arbitrator's decision must be with written explanation and remain confidential. Klub Golf Bogor Raya and you agree that any Covered Dispute will be submitted to arbitration on an individual basis only. If any provision of the agreement to arbitrate in this Section 10 is found unenforceable, the unenforceable provision will be severed and the remaining arbitration terms will be enforced (but in no case will a class, representative or private attorney general arbitration per permitted hereunder). Regardless of any statute or law to the contrary, notice on any claim arising from or related to these Terms must be made within one (1) year after such claim arose or be forever barred. For purposes of this Section 10, these Terms and related transactions will be subject to and governed.</p>
        </div>

        <div class="privacy-section" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="1200">
            <h2>12. ELECTRONIC CONTRACTING</h2>
            <p>Your affirmative act of using and/or registering for the Services constitutes your electronic signature to these Terms, which includes our Privacy Policy, and your consent to enter into agreements with us electronically.</p>
        </div>

        <div class="privacy-section" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="1300">
            <h2>13. GEOGRAPHIC RESTRICTIONS</h2>
            <p>Klub Golf Bogor Raya is based in the state of Bogor in Indonesian. You acknowledge that you may not be able to access the Services outside of Indonesian and that access may not be legal by certain persons or in certain countries. If you access the Services from outside the Indonesian, you are responsible for compliance with local laws. Klub Golf Bogor Raya makes no claims that the Services are accessible or appropriate outside of the Indonesian.</p>

            <p>The Services may be subject to IND export control laws, including the IND Export Administration Act and its associated regulations. You shall not, directly or indirectly, export, re-export or release the Services to, or make the Services accessible from, any jurisdiction or country to which export, re-export or release is prohibited by law, rule or regulation. You shall comply with all applicable federal laws, regulations and rules, and complete all required undertakings (including obtaining any necessary export license or other governmental approval), prior to exporting, re-exporting, releasing or otherwise making the Services available outside the IND</p>
        </div>

        <div class="privacy-section" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="1400">
            <h2>14. TERMINATION</h2>
            <p>These Terms automatically terminate when you fail to comply with any term or condition of them. Klub Golf Bogor Raya may terminate or modify your access to the Services, with or without notice to you, at any time for any reason. For example, access to the Services may be denied without notice if Klub Golf Bogor Raya believes that you are a minor.</p>

            <p>Termination will not limit any of Klub Golf Bogor Raya other rights or remedies. Sections 4, 8, 9, 10 and 15, as well as any other provision that must survive in order to give proper effect to the intent and purpose of these Terms, shall survive termination.</p>
        </div>

        <div class="privacy-section" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="1500">
            <h2>15. CLAIMS OF COPYRIGHT INFRINGEMENT</h2>
            <p>We respond to notices of alleged infringement as required by the IND Digital Millennium Copyright Act ("DMCA"), including by removing or disabling access to material claimed to be the subject of infringing activity. If you have a good faith belief that your work has been copied in a way that constitutes copyright infringement or that your intellectual property rights otherwise have been violated in or through the Services, please send your claim or notice of infringement to our DMCA agent at:</p>

            <p>DMCA Agent<br>
            Klub Golf Bogor Raya<br>
            Bogor Baru, Sukaraja<br>
            Klub Golf Bogor Raya 17610<br>
            Email: copyright@klubgolfbogorraya.com</p>
        </div>

        <div class="privacy-section" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="1600">
            <h2>16. MISCELLANEOUS</h2>
            
            <h3>16.1</h3>
            <p>These Terms including our Privacy Policy contain the entire understanding by and between Klub Golf Bogor Raya and you with respect to the matters contained herein.</p>

            <h3>16.2</h3>
            <p>These Terms inure to the benefit of and will be binding upon Klub Golf Bogor Raya and your successors and assigns, respectively</p>

            <h3>16.3</h3>
            <p>These Terms may be assigned by Klub Golf Bogor Raya, and Klub Golf Bogor Raya may delegate any of its obligations under these Terms, to a buyer or other successor in the event of a merger, divestiture, restructuring, reorganization, dissolution or other sale or transfer of some or all of Klub Golf Bogor Raya assets, whether as a going concern or as part of bankruptcy, liquidation or similar proceeding.</p>

            <h3>16.4</h3>
            <p>If any provision of these Terms is or becomes unenforceable or invalid, the remaining provisions will continue with the same effect as if such unenforceable or invalid provision had not been used.</p>

            <h3>16.5</h3>
            <p>If Klub Golf Bogor Raya fails or you fail to perform any obligation under these Terms and the other party does not enforce such obligation, the failure to enforce on any occasion will not constitute a waiver of any obligation and will not prevent enforcement on any other occasion.</p>

            <h3>16.6</h3>
            <p>Nothing contained in these Terms will be deemed to constitute Klub Golf Bogor Raya or you as the agent or representative of the other or as joint venturers or partners.</p>

            <h3>16.7</h3>
            <p>If Klub Golf Bogor Raya is or you are prevented from performing or unable to perform any obligation under these Terms due to any cause beyond the reasonable control of the party invoking this provision, the affected party's performance will be extended for the period of delay or inability to perform due to such cause.</p>

            <h3>16.8</h3>
            <p>Headings and captions are for convenience only.</p>
        </div>

        <div class="privacy-section contact-info" data-aos="zoom-in" data-aos-duration="1000">
            <h2 style="color:white;">QUESTIONS?</h2>
            <p>If you have questions about these Terms or the Services, please contact us at <a href="mailto:kgbr@bogorraya.co.id">kgbr@bogorraya.co.id</a></p>

            <p>You can also write to us at:</p>

            <p><strong>Klub Golf Bogor Raya</strong><br>
            Attn: Legal Department<br> 
            Jalan Golf Estate, Sukaraja<br>
            Bogor Raya 16710</p>

            <p>If you have questions about our Privacy Policy or to change or delete any information that you provided to us through the Services, please contact us at <a href="mailto:kgbr@bogorraya.co.id">kgbr@bogorraya.co.id</a> or call <a href="tel:+622518271888">(0251) 8271 888</a>.</p>
        </div>
    </div>

    <script>
        // Initialize AOS
        AOS.init({
            once: true,
            offset: 200,
            duration: 1000,
            easing: 'ease-in-out'
        });
    </script>

    <?php include 'includes/footer.php'; ?>
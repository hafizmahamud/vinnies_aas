@extends('layouts.app')

@section('content')
<div class="container">
    @if (session('status'))
        <div class="alert alert-success mb-3">{{ session('status') }}</div>
    @endif

    <div class="row">
        <div class="col-sm-12">
            <div class="tos">
                <div class="alert alert-info mb-3">Please accept the following terms of use in order to continue.</div>
                <h1 class="page-title text-center">Terms of Use</h1>
                <p>You have been nominated by your State/Territory delegated representative to have access to National Council’s Overseas Partnerships Program (OPP) Assist A Student database. </p>
                <p>Your State/Territory delegated representative has advised the OPP that you have:
                    <ul>
                        <li>a National Council or State/Territory Council email address.</li>
                        <li>a current <b>Working with Children / Working with Vulnerable People check or equivalent (as required in your State or Territory)</b> and that you agree to keep the check current through your State or Territory.</li>
                        <li>undergone a police check and are required to update that periodically.</li>
                    </ul>
                    If you do not have all of these items, you need to advise your State/Territory Overseas Partnership Committee Chair immediately.</p>
                <p>As the Twinning database is owned and operated by the St Vincent de Paul Society National Council of Australia Inc. (National Council) your access to it is conditional upon your acceptance of, agreement to, and compliance with, the ‘Terms of Conditions’ (below), notices and disclaimers, and requirements of the National Council’s policy documents you have already signed.</p>
                <p>The National Council’s Policy documents are available below for your reference: </p>
                <p><a href="https://www.vinnies.org.au/national-council/national-council-safeguarding-policy" target="_blank">Safeguarding Policy</a> Confirms the commitment to protect the safety and wellbeing of all children and adults at risk who come into contact with the Society in Australia. </p>
                <p><a href="https://www.vinnies.org.au/national-council/privacy-policy" target="_blank">Privacy Policy</a> Sets out the principles that the St Vincent de Paul Society has adopted in relation to the protection and handling of personal information. </p>
                <p><a href="https://svdpnc.sharepoint.com/:b:/r/sites/NC-OPPGroup/Shared%20Documents/General/National%20Council%27s%20Policy%20Documents/POL_58_NCS_ICTAcceptableUse.pdf" target="_blank">Acceptable Use of ICT Policy</a> Outlines the acceptable use of computer equipment at the St Vincent de Paul Society National Council of Australia to protect the employees and the Society.</p>
                <p><a href="https://svdpnc.sharepoint.com/:b:/r/sites/NC-OPPGroup/Shared%20Documents/General/National%20Council%27s%20Policy%20Documents/POL_59_NCS_CyberSecurity.pdf" target="_blank">Cybersecurity Policy</a> Defines the rules necessary to maintain an adequate level of security to protect the data and information systems of the St Vincent de Paul Society of Australia National Council Inc. from unauthorised access and to ensure a secure and reliable operation of the National Council’s information systems.</p>
                <p><a href="https://www.vinnies.org.au/national-council/working-together-guidelines" target="_blank">Working Together Guidelines</a> Provides a reference for interactions when the Society’s members, volunteers and employees work together. </p>
                <h3>Terms and Conditions </h3>
                <p>The Assist A Student database contains personally identifiable information (PII) data, comments, documents and other communication facilities ("the Database"). By using and accessing the database, you agree that you WILL NOT do any of the following: </p>
                <p><ul>
                <li>the passwords used to grant access to National Council database(s) must comply with the password policy and are not to be forwarded to any other person(s) without the prior agreement of National Council; </li>
                <li>data must not be downloaded to personal computers without password protection being added; </li>
                <li>access or attempt to access information resources you are not authorised to use; </li>
                <li>post, communicate or transmit any unlawful, criminal, threatening, abusive, defamatory, libellous, contemptuous, obscene, vulgar, pornographic, profane or indecent material; </li>
                <li>post, communicate or transmit material which violates or infringes the rights of any other person or party or infringes any law; </li>
                <li>inhibit or restrict any other user from using the Database; </li>
                <li>interfere with the computer systems which support the Database; overload a service; engage in a denial-of-service attack; or attempt to disable a host; </li>
                <li>post, communicate or transmit any file which contains viruses, worms, "Trojan horses" or any other harmful, contaminating or destructive features; </li>
                <li>impersonate or falsely represent your association with any person or organisation; </li>
                <li>attempt to modify, adapt, translate, sell, reverse engineer, decompile or disassemble any portion of the Database, including the use of automated tools; </li>
                <li>post, communicate or transmit or use any material of any kind for commercial purposes, or which contains any promotional material or advertising; </li>
                <li>delete, circumvent or alter any author attribution, legal notices, rights management information or technological protection measures; </li>
                <li>post, download or communicate any file or material posted by another user of the Database if you know, or reasonably ought to know, that the file or material cannot legally be downloaded or communicated in that manner.</li>
                    </ul>
                </p>
                <p>National Council reserves the right to change the “Terms of Use” and ‘Terms and Conditions’ without notification of change. If they change you will be requested to review and accept the changes before being able to continue to use the Database. </p>
                <p>All logos and slogans are subject to copyright obligations to the Society and, along with all information, text, graphics, advertisements, audio, and multimedia found in this Database, are not to be copied, reproduced, reused, reposted, repurposed, transmitted, adapted, republished, broadcast, distributed or uploaded without written authorised permission. </p>
                <p>Transmission of any virus or other harmful component is prohibited. </p>
                <p>The information in the Database is collected and recorded to support the functioning of the Overseas Partnerships Program of the Association and is not for personal or commercial use. </p>
                <h3>Acceptance and Compliance </h3>
                <p>Please confirm your agreement to the ‘Terms of Use’ including the aforementioned policy documents and ‘Terms and Conditions’ set out above. </p>

                <form action="{{ route('home.tos') }}" method="post">
                    @csrf

                    @foreach ($checkboxes as $term => $label)
                        <p>
                            <label><input type="checkbox" name="terms[]" value="{{ $term }}"> <span style="font-weight: normal;">{{ $label }}</span></label>
                        </p>
                    @endforeach

                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
                <h3>For Further Information   </h3>
                <p>Please contact Tricia Wilden <a href="mailto:overseasdevelopment@svdp.org.au" >(overseasdevelopment@svdp.org.au)</a>.</p>

            </div>
        </div>
    </div>
</div>
@endsection

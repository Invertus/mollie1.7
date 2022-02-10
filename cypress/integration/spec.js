/// <reference types="Cypress" />

context('Cypress test', () => {
  beforeEach(() => {   
  })
it('200 response from homepage', () => {
  const https = require('https')
  const cert = require('https-pem')

  const port = process.env.PORT || 12345

  const server = https.createServer({
   //secureProtocol: 'TLSv1_2_server_method',
   secureProtocol: 'TLSv1_server_method',
  ...cert
}, (req, res) => {
  res.setHeader('content-type', 'text/html') // required by Cypress cy.visit
  res.end('foo')
})

server.listen(port, () => {
  console.log(`listening at https://127.0.0.1:${port}`)
})
  cy.visit('/admin1')
  cy.get('#email').type('demo@prestashop.com',{delay: 0, log: false})
  cy.get('#passwd').type('prestashop_demo',{delay: 0, log: false})
  cy.get('#submit_login').click()
  cy.contains('Mollie')
})
it('URl check', () => {
  cy.visit('/')
  cy.url().should('include','https')
})
})